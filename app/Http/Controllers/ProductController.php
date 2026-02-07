<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    // Fungsi untuk menyimpan produk baru
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'kode' => 'required|unique:products',
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        // 2. Cari Gambar Otomatis via Serper
        $imageUrl = $this->cariGambarSerper($validated['nama']);

        // Jika tidak ketemu, pakai placeholder
        if (!$imageUrl) {
            $imageUrl = 'https://via.placeholder.com/300x300?text=No+Image';
        }

        // 3. Simpan ke Database
        Product::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'stok' => $validated['stok'],
            'image_url' => $imageUrl
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan dengan gambar otomatis!');
    }

    // --- LOGIKA PENCARIAN SERPER ---
    private function cariGambarSerper(string $keyword): ?string
    {
        // ✅ BARIS INI SUDAH MENGGUNAKAN CONFIG
        $apiKey = config('services.serper.api_key');

        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json'
            ])
            ->timeout(10)
            ->post('https://google.serper.dev/images', [
                'q' => $keyword . ' kemasan produk indonesia',
                'gl' => 'id',
                'num' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['images'][0]['imageUrl'] ?? null;
            }

        } catch (\Exception $e) {
            \Log::error('Serper API Error: ' . $e->getMessage());
            return null;
        }

        return null;
    }
}