<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class PosController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('pos.dashboard', compact('products'));
    }

    public function bayar(Request $request) {
        // ... (Validasi Admin Diskon TETAP SAMA seperti sebelumnya) ...
        $user = Auth::user();
        $diskon = $request->diskon ?? 0;
        $bayar = $request->bayar ?? 0;
        $cart = $request->cart;
        
        $adminId = null;
        if ($user->role == 'kasir' && $diskon > 10000) {
            $admin = User::where('role', 'admin')->first();
            if (!$admin || !Hash::check($request->admin_password, $admin->password)) {
                return response()->json(['status' => 'error', 'msg' => 'Diskon > 10rb butuh Persetujuan Admin!'], 403);
            }
            $adminId = $admin->id;
        }

        DB::beginTransaction();
        try {
            // 1. Hitung Subtotal & Grand Total
            $subtotal = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if($product->stok < $item['qty']) throw new \Exception("Stok " . $product->nama . " habis!");
                $subtotal += $product->harga * $item['qty'];
            }
            $grandTotal = $subtotal - $diskon;

            // --- REVISI DISINI: VALIDASI UNTUK SEMUA KECUALI UTANG ---
            // Jika metode BUKAN 'utang' (berarti Cash, Transfer, atau QRIS)
            // Maka nominal bayar TIDAK BOLEH kurang dari Grand Total
            if ($request->payment_method !== 'utang') {
                if ($bayar < $grandTotal) {
                    throw new \Exception("Nominal Pembayaran Kurang! Total harus: Rp " . number_format($grandTotal, 0, ',', '.'));
                }
            }
            
            $statusTransaksi = ($request->payment_method == 'utang' || $request->payment_method == 'qris') ? 'belum_lunas' : 'lunas';

            $sale = Sale::create([
                'no_faktur' => 'INV-' . time(),
                'user_id' => $user->id,
                'customer_name' => $request->customer_name ?? 'Umum',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'grand_total' => $grandTotal,
                'bayar' => $bayar,
                'status' => $statusTransaksi,
                'approved_by' => $adminId
            ]);

            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'harga_saat_itu' => $product->harga,
                    'subtotal_line' => $product->harga * $item['qty']
                ]);
                $product->decrement('stok', $item['qty']);
            }

            // --- LOGIKA MIDTRANS ---
            $snapToken = null;
            if ($request->payment_method == 'qris') {
                 // ... (Kode Midtrans Tetap Sama) ...
                 Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                 Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
                 Config::$isSanitized = true;
                 Config::$is3ds = true;
 
                 $params = [
                     'transaction_details' => [
                         'order_id' => $sale->no_faktur,
                         'gross_amount' => (int) $grandTotal,
                     ],
                     'customer_details' => [
                         'first_name' => $request->customer_name ?? 'Pelanggan',
                         'email' => 'kasir@sumberbangunan.com',
                     ],
                 ];
                 $snapToken = Snap::getSnapToken($params);
            }

            DB::commit();

            return response()->json([
                'status' => 'success', 
                'msg' => 'Transaksi Dibuat',
                'sale_id' => $sale->id,
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    // --- FUNGSI CALLBACK (WEBHOOK) ---
    public function callback(Request $request) {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);

        if($hashed == $request->signature_key){
            $sale = Sale::where('no_faktur', $request->order_id)->first();
            if($sale) {
                if($request->transaction_status == 'capture' || $request->transaction_status == 'settlement'){
                    $sale->update(['status' => 'lunas']);
                } elseif($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny'){
                    $sale->update(['status' => 'batal']);
                    // Opsional: Kembalikan stok jika batal (perlu logika tambahan)
                }
            }
        }
        return response()->json(['status' => 'ok']);
    }

    public function cetakStruk($id) {
        $sale = Sale::with(['items.product', 'user'])->findOrFail($id);
        return view('pos.struk', compact('sale'));
    }
    
}