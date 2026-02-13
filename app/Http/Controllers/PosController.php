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
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

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
            
            $statusTransaksi = ($request->payment_method == 'utang' || $request->payment_method == 'online') ? 'belum_lunas' : 'lunas';

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

            $invoiceUrl = null;
            
            if ($request->payment_method == 'online') {
                 Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
                 $apiInstance = new InvoiceApi();
                 
                 $create_invoice_request = new CreateInvoiceRequest([
                    'external_id' => $sale->no_faktur,
                    'amount' => $grandTotal,
                    'payer_email' => 'kasir@sumberbangunan.com', // Email toko atau pelanggan
                    'description' => 'Pembayaran Kasir No: ' . $sale->no_faktur,
                    'invoice_duration' => 172800, // 2 Hari
                    // Redirect kembali ke dashboard setelah sukses dengan parameter sale_id
                    'success_redirect_url' => route('dashboard') . '?sale_id=' . $sale->id . '&payment_success=1', 
                    'failure_redirect_url' => route('dashboard') . '?payment_failed=1'
                 ]);

                 try {
                    $result = $apiInstance->createInvoice($create_invoice_request);
                    $invoiceUrl = $result['invoice_url'];
                 } catch (\Exception $e) {
                    throw new \Exception("Gagal membuat Invoice Xendit: " . $e->getMessage());
                 }
            }

            DB::commit();

            return response()->json([
                'status' => 'success', 
                'msg' => 'Transaksi Dibuat',
                'sale_id' => $sale->id,
                'invoice_url' => $invoiceUrl // Ubah dari snap_token ke invoice_url
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    // --- FUNGSI CALLBACK (WEBHOOK) ---
    public function callback(Request $request) {
        // Ambil token dari header Xendit
        $xenditXCallbackToken = $request->header('x-callback-token');
        
        // Verifikasi token (Pastikan sesuai dengan di .env)
        if ($xenditXCallbackToken !== env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json(['status' => 'error', 'message' => 'Token Salah'], 403);
        }

        // Ambil data dari body request
        $external_id = $request->external_id;
        $status = $request->status;

        $sale = Sale::where('no_faktur', $external_id)->first();

        if($sale) {
            if ($status == 'PAID' || $status == 'SETTLED') {
                $sale->update(['status' => 'lunas']);
            } else if ($status == 'EXPIRED') {
                $sale->update(['status' => 'batal']);
                // Logika kembalikan stok bisa ditaruh disini jika perlu
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // --- FUNGSI CEK STATUS INVOICE XENDIT ---
    public function cekStatusInvoice($saleId) {
        try {
            $sale = Sale::findOrFail($saleId);
            
            // Jika sudah lunas atau batal, tidak perlu cek lagi
            if ($sale->status == 'lunas' || $sale->status == 'batal') {
                return response()->json([
                    'status' => 'success',
                    'payment_status' => $sale->status
                ]);
            }

            // Cek status invoice dari Xendit API
            Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
            $apiInstance = new InvoiceApi();
            
            try {
                // Get all invoices dengan filter external_id
                $invoices = $apiInstance->getInvoices(
                    null,           // statuses
                    null,           // limit
                    null,           // created_after
                    null,           // created_before
                    null,           // payer_email
                    null,           // client_type
                    null,           // payment_channels
                    null,           // on_demand_link
                    null,           // recurring_payment_id
                    null,           // for_user_id
                    $sale->no_faktur  // external_id
                );
                
                if (!empty($invoices) && count($invoices) > 0) {
                    $invoice = $invoices[0];
                    $xenditStatus = strtoupper($invoice['status']);
                    
                    \Log::info("Xendit Status Check", [
                        'no_faktur' => $sale->no_faktur,
                        'xendit_status' => $xenditStatus,
                        'sale_id' => $sale->id
                    ]);
                    
                    // Update status di database sesuai status dari Xendit
                    if ($xenditStatus == 'PAID' || $xenditStatus == 'SETTLED') {
                        $sale->update(['status' => 'lunas']);
                        return response()->json([
                            'status' => 'success',
                            'payment_status' => 'lunas',
                            'xendit_status' => $xenditStatus
                        ]);
                    } else if ($xenditStatus == 'EXPIRED') {
                        $sale->update(['status' => 'batal']);
                        return response()->json([
                            'status' => 'success',
                            'payment_status' => 'batal',
                            'xendit_status' => $xenditStatus
                        ]);
                    } else {
                        // Status masih pending
                        return response()->json([
                            'status' => 'success',
                            'payment_status' => 'belum_lunas',
                            'xendit_status' => $xenditStatus
                        ]);
                    }
                } else {
                    // Invoice tidak ditemukan
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => $sale->status,
                        'note' => 'Invoice tidak ditemukan di Xendit'
                    ]);
                }
            } catch (\Xendit\XenditSdkException $e) {
                // Error dari Xendit SDK
                \Log::error("Xendit API Error", [
                    'message' => $e->getMessage(),
                    'no_faktur' => $sale->no_faktur
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'payment_status' => $sale->status,
                    'note' => 'Error dari Xendit: ' . $e->getMessage()
                ]);
            } catch (\Exception $e) {
                // Error umum
                \Log::error("General Error", [
                    'message' => $e->getMessage(),
                    'no_faktur' => $sale->no_faktur
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'payment_status' => $sale->status,
                    'note' => 'Error: ' . $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // --- FUNGSI UPDATE SEMUA TRANSAKSI ONLINE YANG PENDING ---
    public function updateSemuaStatusOnline() {
        try {
            // Ambil semua transaksi online yang belum lunas
            $pendingSales = Sale::where('payment_method', 'online')
                                ->where('status', 'belum_lunas')
                                ->orderBy('created_at', 'desc')
                                ->get();
            
            if ($pendingSales->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada transaksi pending',
                    'updated' => 0,
                    'total_checked' => 0
                ]);
            }

            Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
            $apiInstance = new InvoiceApi();
            
            $updated = 0;
            $results = [];

            foreach ($pendingSales as $sale) {
                try {
                    // Get invoice by external_id
                    $invoices = $apiInstance->getInvoices(
                        null,           // statuses
                        null,           // limit
                        null,           // created_after
                        null,           // created_before
                        null,           // payer_email
                        null,           // client_type
                        null,           // payment_channels
                        null,           // on_demand_link
                        null,           // recurring_payment_id
                        null,           // for_user_id
                        $sale->no_faktur  // external_id
                    );
                    
                    if (!empty($invoices) && count($invoices) > 0) {
                        $invoice = $invoices[0];
                        $xenditStatus = strtoupper($invoice['status']);
                        
                        // Log untuk debugging
                        \Log::info("Sync Payment Check", [
                            'no_faktur' => $sale->no_faktur,
                            'xendit_status' => $xenditStatus,
                            'current_db_status' => $sale->status
                        ]);
                        
                        // Update status di database
                        if ($xenditStatus == 'PAID' || $xenditStatus == 'SETTLED') {
                            $sale->update(['status' => 'lunas']);
                            $updated++;
                            $results[] = [
                                'no_faktur' => $sale->no_faktur,
                                'status' => 'lunas',
                                'xendit_status' => $xenditStatus
                            ];
                        } else if ($xenditStatus == 'EXPIRED') {
                            $sale->update(['status' => 'batal']);
                            $updated++;
                            $results[] = [
                                'no_faktur' => $sale->no_faktur,
                                'status' => 'batal',
                                'xendit_status' => $xenditStatus
                            ];
                        } else {
                            // Masih pending
                            $results[] = [
                                'no_faktur' => $sale->no_faktur,
                                'status' => 'pending',
                                'xendit_status' => $xenditStatus
                            ];
                        }
                    } else {
                        $results[] = [
                            'no_faktur' => $sale->no_faktur,
                            'error' => 'Invoice tidak ditemukan di Xendit'
                        ];
                    }
                    
                    // Delay 300ms untuk menghindari rate limit
                    usleep(300000);
                    
                } catch (\Xendit\XenditSdkException $e) {
                    \Log::error("Xendit SDK Error", [
                        'no_faktur' => $sale->no_faktur,
                        'error' => $e->getMessage()
                    ]);
                    
                    $results[] = [
                        'no_faktur' => $sale->no_faktur,
                        'error' => 'Xendit Error: ' . $e->getMessage()
                    ];
                } catch (\Exception $e) {
                    \Log::error("General Error", [
                        'no_faktur' => $sale->no_faktur,
                        'error' => $e->getMessage()
                    ]);
                    
                    $results[] = [
                        'no_faktur' => $sale->no_faktur,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "$updated transaksi berhasil diupdate",
                'total_checked' => $pendingSales->count(),
                'updated' => $updated,
                'details' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error("Update Semua Status Error", [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cetakStruk($id) {
        $sale = Sale::with(['items.product', 'user'])->findOrFail($id);
        return view('pos.struk', compact('sale'));
    }

    // --- FUNGSI MANUAL UPDATE STATUS (UNTUK TESTING/EMERGENCY) ---
    public function manualUpdateStatus(Request $request, $saleId) {
        try {
            $sale = Sale::findOrFail($saleId);
            
            $request->validate([
                'status' => 'required|in:lunas,belum_lunas,batal'
            ]);
            
            $sale->update(['status' => $request->status]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status berhasil diupdate ke: ' . $request->status,
                'sale' => $sale
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}