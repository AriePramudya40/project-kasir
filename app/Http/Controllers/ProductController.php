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
                'kode' => strtoupper(trim($validated['kode'])), // Normalisasi kode
                'nama' => ucwords(trim($validated['nama'])), // Normalisasi nama
                'harga' => $validated['harga'],
                'stok' => $validated['stok'],
                'image_url' => $imageUrl
            ]);

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan dengan gambar otomatis!');
            
        } catch (\Exception $e) {
            Log::error('Error menyimpan produk: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan produk. Silakan coba lagi.');
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
        // Ambil API key dari config
        $apiKey = config('services.serper.api_key');

        // Jika API key tidak tersedia, return null
        if (empty($apiKey)) {
            Log::warning('Serper API key tidak ditemukan di config');
            return null;
        }

        try {
            // Request ke Serper API
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])
            ->timeout(10)
            ->retry(2, 100) // Retry 2x jika gagal
            ->post('https://google.serper.dev/images', [
                'q' => $keyword . ' kemasan produk indonesia',
                'gl' => 'id',
                'hl' => 'id',
                'num' => 1
            ]);

            // Cek apakah request berhasil
            if ($response->successful()) {
                $data = $response->json();
                
                // Validasi struktur response
                if (isset($data['images']) && is_array($data['images']) && count($data['images']) > 0) {
                    $imageUrl = $data['images'][0]['imageUrl'] ?? null;
                    
                    // Validasi URL gambar
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