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
use Illuminate\Support\Facades\Log;

// --- PENTING: Panggil Library Midtrans ---
use Midtrans\Config;
use Midtrans\Snap;

class PosController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('pos.dashboard', compact('products'));
    }

    public function bayar(Request $request) {
        $user = Auth::user();
        $cart = $request->cart;
        $diskon = $request->input('diskon', 0);
        
        // Ambil data metode bayar & uang
        $metode = $request->input('metode', 'tunai'); // 'tunai' atau 'online'
        $uangBayar = $request->input('uang_bayar', 0); 
        
        $adminId = null;

        // 1. Cek Otorisasi Admin untuk Diskon Besar
        if ($user->role == 'kasir' && $diskon > 10000) {
            $admin = User::where('role', 'admin')->first();
            if (!$admin || !Hash::check($request->admin_password, $admin->password)) {
                return response()->json(['status' => 'error', 'msg' => 'Diskon > 10rb butuh Persetujuan Admin!'], 403);
            }
            $adminId = $admin->id;
        }

        DB::beginTransaction();
        try {
            // 2. Hitung Total Belanja
            $subtotal = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                $subtotal += $product->harga * $item['qty'];
            }
            $grandTotal = $subtotal - $diskon;

            // 3. Validasi Uang (KHUSUS TUNAI)
            // Kalau Online, kita lewati pengecekan ini karena uangnya pasti pas
            if ($metode == 'tunai' && $uangBayar < $grandTotal) {
                return response()->json(['status' => 'error', 'msg' => 'Uang Pembayaran Kurang!']);
            }

            // 4. Tentukan Status Awal
            // Tunai = Langsung Lunas. Online = Pending (Nunggu Customer Scan QRIS)
            $statusAwal = ($metode == 'tunai') ? 'lunas' : 'pending';

            // 5. Simpan Header Transaksi
            $sale = Sale::create([
                'no_faktur' => 'INV-' . time(),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'bayar' => $uangBayar, // Kalau online ini akan 0, tidak apa-apa
                'grand_total' => $grandTotal,
                'status' => $statusAwal, // <--- Ini Penting!
                'approved_by' => $adminId,
            ]);

            // 6. Simpan Detail Barang & Kurangi Stok
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

            // Siapkan Response Awal
            $response = [
                'status' => 'success',
                'sale_id' => $sale->id,
                'metode' => $metode,
            ];

            // 7. LOGIKA KHUSUS ONLINE (Minta Token Midtrans)
            if ($metode == 'online') {
                // Set Konfigurasi
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = true;
                Config::$is3ds = true;

                // Data untuk Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $sale->no_faktur, // PENTING: No Faktur jadi Order ID
                        'gross_amount' => $grandTotal,
                    ],
                    'customer_details' => [
                        'first_name' => 'Pelanggan Umum',
                        'email' => 'kasir@toko.com',
                    ],
                ];

                // Minta Snap Token
                $snapToken = Snap::getSnapToken($params);
                $response['snap_token'] = $snapToken; // Kirim token ini ke Dashboard
            } else {
                // Kalau Tunai, kirim kembalian
                $response['msg'] = number_format($uangBayar - $grandTotal, 0, ',', '.');
            }

            DB::commit();
            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function cetakStruk($id) {
        $sale = Sale::with(['items.product', 'user'])->findOrFail($id);
        return view('pos.struk', compact('sale'));
    }

    public function callback(Request $request) {
        // CCTV 1: Cek apakah Midtrans mengetuk pintu?
        Log::info('🔔 Callback Midtrans Masuk!');

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notif = new \Midtrans\Notification();
            
            $status = $notif->transaction_status;
            $type = $notif->payment_type;
            $fraud = $notif->fraud_status;
            $order_id = $notif->order_id;

            // CCTV 2: Catat data apa yang dibawa Midtrans
            Log::info("Data Masuk -> Order ID: $order_id | Status: $status");

            $sale = Sale::where('no_faktur', $order_id)->first();

            if (!$sale) {
                // CCTV 3: Kalau data tidak ketemu
                Log::error("❌ Transaksi tidak ditemukan di database: $order_id");
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Logika Status Midtrans
            if ($status == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $sale->update(['status' => 'pending']);
                    } else {
                        $sale->update(['status' => 'lunas']);
                    }
                }
            } else if ($status == 'settlement') {
                // Settlement = Uang sudah masuk (Sukses)
                $sale->update(['status' => 'lunas']);
                
                // CCTV 4: Berhasil Update
                Log::info("✅ Berhasil update status LUNAS untuk: $order_id");

            } else if ($status == 'pending') {
                $sale->update(['status' => 'pending']);
            } else if ($status == 'deny') {
                $sale->update(['status' => 'batal']);
            } else if ($status == 'expire') {
                $sale->update(['status' => 'batal']);
            } else if ($status == 'cancel') {
                $sale->update(['status' => 'batal']);
            }

            return response()->json(['message' => 'Callback received successfully']);

        } catch (\Exception $e) {
            // CCTV 5: Kalau ada error kodingan
            Log::error("🔥 Error Exception: " . $e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
}