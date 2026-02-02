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

class PosController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('pos.dashboard', compact('products'));
    }

    public function bayar(Request $request) {
        $user = Auth::user();
        $diskon = $request->diskon;
        $cart = $request->cart;
        $adminId = null;

        // Aturan Diskon Kasir: Maks 10rb tanpa PIN Admin
        if ($user->role == 'kasir' && $diskon > 10000) {
            $admin = User::where('role', 'admin')->first();
            if (!$admin || !Hash::check($request->admin_password, $admin->password)) {
                return response()->json(['status' => 'error', 'msg' => 'Diskon > 10rb butuh Persetujuan Admin!'], 403);
            }
            $adminId = $admin->id;
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                $subtotal += $product->harga * $item['qty'];
            }

            // Simpan Header Penjualan
            $sale = Sale::create([
                'no_faktur' => 'INV-' . time(),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'approved_by' => $adminId,
                'grand_total' => $subtotal - $diskon,
                'status' => 'lunas'
            ]);

            // Simpan Detail & Kurangi Stok
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

            DB::commit();
            return response()->json([
                'status' => 'success', 
                'msg' => 'Transaksi Berhasil!',
                'sale_id' => $sale->id // Dipakai JS untuk buka struk
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function cetakStruk($id) {
        // Load sale beserta relasi items dan product-nya
        $sale = Sale::with('items.product')->findOrFail($id);
        return view('pos.struk', compact('sale'));
    }
}