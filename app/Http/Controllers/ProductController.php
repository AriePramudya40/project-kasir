<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Menyimpan produk baru dengan gambar otomatis dari Serper API
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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

        // 2. Cari Gambar Otomatis via Serper API
        $imageUrl = $this->cariGambarSerper($validated['nama']);

        // Jika tidak ketemu, gunakan placeholder
        if (!$imageUrl) {
            $imageUrl = 'https://via.placeholder.com/300x300?text=No+Image';
        }

        // 3. Simpan ke Database
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
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Cari produk
        $product = Product::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'kode' => 'required|unique:products,kode,' . $id,
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'refresh_image' => 'nullable|boolean', // Opsi untuk refresh gambar
        ], [
            'kode.required' => 'Kode barang wajib diisi',
            'kode.unique' => 'Kode barang sudah digunakan',
            'nama.required' => 'Nama barang wajib diisi',
            'harga.required' => 'Harga wajib diisi',
            'harga.min' => 'Harga tidak boleh negatif',
            'stok.required' => 'Stok wajib diisi',
            'stok.min' => 'Stok tidak boleh negatif',
        ]);

        try {
            // Update data dasar
            $product->kode = strtoupper(trim($validated['kode']));
            $product->nama = ucwords(trim($validated['nama']));
            $product->harga = $validated['harga'];
            $product->stok = $validated['stok'];

            // Jika user request refresh gambar atau nama berubah
            if ($request->has('refresh_image') || $product->isDirty('nama')) {
                $newImageUrl = $this->cariGambarSerper($validated['nama']);
                if ($newImageUrl) {
                    $product->image_url = $newImageUrl;
                }
            }

            $product->save();

            return redirect()->back()->with('success', 'Produk berhasil diupdate!');
            
        } catch (\Exception $e) {
            Log::error('Error update produk: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengupdate produk.');
        }
    }

    /**
     * Hapus produk
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
{
    try {
        $product = Product::findOrFail($id);
        $namaBarang = $product->nama;
        
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Produk '{$namaBarang}' berhasil dihapus!"
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error hapus produk: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus produk: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get single product data (untuk AJAX)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
    try {
        $product = Product::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $product->id,
                'kode' => $product->kode,
                'nama' => $product->nama,
                'harga' => $product->harga,
                'stok' => $product->stok,
                'image_url' => $product->image_url
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching product: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }
    }

    /**
     * Mencari gambar produk menggunakan Serper API
     *
     * @param string $keyword
     * @return string|null
     */
    private function cariGambarSerper(string $keyword): ?string
    {
        $apiKey = config('services.serper.api_key');

        if (empty($apiKey)) {
            Log::warning('Serper API key tidak ditemukan di config');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])
            ->timeout(10)
            ->retry(2, 100)
            ->post('https://google.serper.dev/images', [
                'q' => $keyword . ' kemasan produk indonesia',
                'gl' => 'id',
                'hl' => 'id',
                'num' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['images']) && is_array($data['images']) && count($data['images']) > 0) {
                    $imageUrl = $data['images'][0]['imageUrl'] ?? null;
                    
                    if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        return $imageUrl;
                    }
                }
            } else {
                Log::warning('Serper API response error: ' . $response->status());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Serper API connection error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Serper API unexpected error: ' . $e->getMessage());
        }

        return null;
    }
}