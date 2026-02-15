<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment; // Pastikan Model ini ada!
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class PosController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('pos.dashboard', compact('products'));
    }

    // --- 1. PEMBAYARAN BARU (DASHBOARD) ---
    public function bayar(Request $request) {
        $user = Auth::user();
        $diskon = $request->diskon ?? 0;
        $bayar = $request->bayar ?? 0;
        $cart = $request->cart;
        
        // Validasi Admin
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
            // Hitung Total
            $subtotal = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if($product->stok < $item['qty']) throw new \Exception("Stok " . $product->nama . " habis!");
                $subtotal += $product->harga * $item['qty'];
            }
            $grandTotal = $subtotal - $diskon;

            // Validasi Bayar Awal
            if ($request->payment_method !== 'utang') {
                if ($bayar < $grandTotal) throw new \Exception("Nominal Kurang! Total: Rp " . number_format($grandTotal, 0, ',', '.'));
            }
            
            // Simpan Transaksi
            $sale = Sale::create([
                'no_faktur' => 'INV-' . time(),
                'user_id' => $user->id,
                'customer_name' => $request->customer_name ?? 'Umum',
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'grand_total' => $grandTotal,
                'bayar' => $bayar,
                'status' => ($request->payment_method == 'utang' || $request->payment_method == 'online') ? 'belum_lunas' : 'lunas',
                'approved_by' => $adminId
            ]);

            // Simpan Item
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                SaleItem::create([
                    'sale_id' => $sale->id, 'product_id' => $item['id'], 'qty' => $item['qty'],
                    'harga_saat_itu' => $product->harga, 'subtotal_line' => $product->harga * $item['qty']
                ]);
                $product->decrement('stok', $item['qty']);
            }

            // Xendit Logic
            $invoiceUrl = null;
            if ($request->payment_method == 'online') {
                 Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
                 $apiInstance = new InvoiceApi();
                 $create_invoice_request = new CreateInvoiceRequest([
                    'external_id' => $sale->no_faktur,
                    'amount' => $grandTotal,
                    'payer_email' => 'kasir@store.com',
                    'description' => 'Pembayaran #'. $sale->no_faktur,
                    'invoice_duration' => 86400,
                    'success_redirect_url' => route('dashboard') . '?sale_id=' . $sale->id . '&payment_success=1', 
                    'failure_redirect_url' => route('dashboard') . '?payment_failed=1'
                 ]);
                 $result = $apiInstance->createInvoice($create_invoice_request);
                 $invoiceUrl = $result['invoice_url'];
            }

            DB::commit();
            return response()->json(['status' => 'success', 'sale_id' => $sale->id, 'invoice_url' => $invoiceUrl]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    // --- 2. CICILAN / PELUNASAN (LOGIKA SAMA DENGAN BAYAR) ---
    public function lunasiPiutang(Request $request, $id) {
        $user = Auth::user();
        $sale = Sale::findOrFail($id);
        
        $nominalBayar = (int) $request->bayar_nominal;
        $method = $request->payment_method;
        $sisa = $sale->grand_total - $sale->bayar;

        if($nominalBayar > $sisa) return response()->json(['status' => 'error', 'message' => 'Melebihi sisa hutang'], 400);

        DB::beginTransaction();
        try {
            if ($method == 'cash') {
                // Tunai: Langsung catat & update
                SalePayment::create([
                    'sale_id' => $sale->id, 'user_id' => $user->id,
                    'nominal' => $nominalBayar, 'payment_method' => 'cash'
                ]);
                
                $newBayar = $sale->bayar + $nominalBayar;
                $sale->update([
                    'bayar' => $newBayar,
                    'status' => ($newBayar >= $sale->grand_total) ? 'lunas' : 'belum_lunas'
                ]);

                DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Pembayaran Tunai Berhasil']);

            } else {
                // Online: Buat Invoice -> Return URL
                Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
                $apiInstance = new InvoiceApi();
                
                // Buat External ID unik untuk cicilan ini
                $external_id = $sale->no_faktur . '-CICIL-' . time();

                $create_invoice_request = new CreateInvoiceRequest([
                    'external_id' => $external_id,
                    'amount' => $nominalBayar,
                    'payer_email' => 'pelanggan@example.com',
                    'description' => 'Cicilan #'. $sale->no_faktur,
                    'invoice_duration' => 86400,
                    // Redirect membawa parameter agar Frontend bisa auto-check (mirip dashboard)
                    'success_redirect_url' => route('laporan.index') . '?status_cicilan=success&external_id=' . $external_id, 
                    'failure_redirect_url' => route('laporan.index')
                ]);

                $result = $apiInstance->createInvoice($create_invoice_request);
                
                DB::commit();
                return response()->json(['status' => 'success', 'type' => 'online', 'invoice_url' => $result['invoice_url']]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // --- 3. VERIFIKASI CICILAN (LOGIKA SAMA DENGAN cekStatusInvoice) ---
    public function cekStatusCicilan($external_id) {
        try {
            Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
            $apiInstance = new InvoiceApi();
            
            // Cek Invoice ke Xendit
            $invoices = $apiInstance->getInvoices(null, null, null, null, null, null, null, null, null, null, $external_id);
            
            if (empty($invoices)) return response()->json(['status' => 'error', 'message' => 'Invoice tidak ditemukan']);
            $invoice = $invoices[0];

            if ($invoice['status'] == 'PAID' || $invoice['status'] == 'SETTLED') {
                
                // Parse External ID untuk dapat Sale Asli
                $parts = explode('-CICIL-', $external_id);
                $sale = Sale::where('no_faktur', $parts[0])->first();
                
                if (!$sale) return response()->json(['status' => 'error', 'message' => 'Transaksi Asli Tidak Ada']);

                // Cek Idempotency (Jangan catat 2x)
                $cek = SalePayment::where('external_id', $external_id)->first();
                if($cek) return response()->json(['status' => 'success', 'amount' => $cek->nominal]);

                DB::beginTransaction();
                try {
                    // Catat Pembayaran
                    SalePayment::create([
                        'sale_id' => $sale->id, 'user_id' => Auth::id() ?? 1,
                        'nominal' => $invoice['amount'], 'payment_method' => 'online',
                        'external_id' => $external_id
                    ]);

                    // Update Saldo
                    $newBayar = $sale->bayar + $invoice['amount'];
                    if($newBayar > $sale->grand_total) $newBayar = $sale->grand_total; 

                    $sale->update([
                        'bayar' => $newBayar,
                        'status' => ($newBayar >= $sale->grand_total) ? 'lunas' : 'belum_lunas'
                    ]);

                    DB::commit();
                    return response()->json(['status' => 'success', 'amount' => $invoice['amount']]);

                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
                }
            } else {
                return response()->json(['status' => 'pending', 'message' => 'Menunggu pembayaran']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // --- FUNGSI LAINNYA (Webhooks, Print, dll) ---
    public function callback(Request $request) {
        $xenditXCallbackToken = $request->header('x-callback-token');
        if ($xenditXCallbackToken !== env('XENDIT_CALLBACK_TOKEN')) return response()->json(['status' => 'error'], 403);

        $external_id = $request->external_id;
        $status = $request->status;
        
        // Handle Pembayaran Utama
        $sale = Sale::where('no_faktur', $external_id)->first();
        if($sale) {
            if ($status == 'PAID' || $status == 'SETTLED') $sale->update(['status' => 'lunas']);
            else if ($status == 'EXPIRED') $sale->update(['status' => 'batal']);
        }
        return response()->json(['status' => 'ok']);
    }

    public function cekStatusInvoice($saleId) {
        try {
            $sale = Sale::findOrFail($saleId);
            if ($sale->status == 'lunas') return response()->json(['status' => 'success', 'payment_status' => 'lunas']);
            
            Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
            $apiInstance = new InvoiceApi();
            $invoices = $apiInstance->getInvoices(null, null, null, null, null, null, null, null, null, null, $sale->no_faktur);
            
            if (!empty($invoices) && ($invoices[0]['status'] == 'PAID' || $invoices[0]['status'] == 'SETTLED')) {
                $sale->update(['status' => 'lunas']);
                return response()->json(['status' => 'success', 'payment_status' => 'lunas']);
            }
            return response()->json(['status' => 'success', 'payment_status' => 'belum_lunas']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateSemuaStatusOnline() { return response()->json(['status' => 'success']); }

    public function cetakStruk($id) {
        // Tambahkan 'payments' di dalam with([...])
        $sale = Sale::with(['items.product', 'user', 'payments'])->findOrFail($id);
        
        return view('pos.struk', compact('sale'));
    }

    public function manualUpdateStatus(Request $request, $saleId) {
        Sale::findOrFail($saleId)->update(['status' => $request->status]);
        return response()->json(['status' => 'success']);
    }
}