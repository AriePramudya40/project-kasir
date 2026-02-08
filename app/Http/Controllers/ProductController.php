<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'kode' => 'required|unique:products,kode',
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ], [
            'kode.required' => 'Kode barang wajib diisi',
            'kode.unique' => 'Kode barang sudah digunakan',
            'nama.required' => 'Nama barang wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'harga.min' => 'Harga tidak boleh negatif',
            'stok.required' => 'Stok wajib diisi',
            'stok.min' => 'Stok tidak boleh negatif',
        ]);

        // 2. Cari Gambar Otomatis
        $imageUrl = $this->cariGambarSerper($validated['nama']);
        if (!$imageUrl) {
            $imageUrl = 'https://via.placeholder.com/300x300?text=No+Image';
        }

        // 3. Simpan
        try {
            Product::create([
                'kode' => strtoupper(trim($validated['kode'])),
                'nama' => ucwords(trim($validated['nama'])),
                'harga' => $validated['harga'],
                'stok' => $validated['stok'],
                'image_url' => $imageUrl
            ]);

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            Log::error('Error menyimpan produk: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan produk.');
        }
    }

    /**
     * Update produk yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 1. Validasi (Kode boleh sama jika milik produk ini sendiri)
        $validated = $request->validate([
            'kode' => 'required|unique:products,kode,'.$id,
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        try {
            // Jika nama berubah, kita cari gambar baru (opsional, bisa dimatikan jika tidak ingin ganti gambar)
            if ($request->nama !== $product->nama) {
                 $imageUrl = $this->cariGambarSerper($request->nama);
                 if ($imageUrl) {
                     $product->image_url = $imageUrl;
                 }
            }

            $product->update([
                'kode' => strtoupper(trim($validated['kode'])),
                'nama' => ucwords(trim($validated['nama'])),
                'harga' => $validated['harga'],
                'stok' => $validated['stok'],
                // image_url diupdate di atas jika nama berubah
            ]);

            return redirect()->back()->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->back()->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus produk (Mungkin sudah ada transaksi).');
        }
    }

    /**
     * Mencari gambar produk via Serper
     */
    private function cariGambarSerper(string $keyword): ?string
    {
        $apiKey = config('services.serper.api_key');
        if (empty($apiKey)) return null;

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])->timeout(5)->post('https://google.serper.dev/images', [
                'q' => $keyword . ' kemasan produk indonesia',
                'gl' => 'id', 'hl' => 'id', 'num' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['images'][0]['imageUrl'])) {
                    return $data['images'][0]['imageUrl'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Serper API Error: ' . $e->getMessage());
        }
        return null;
    }
}