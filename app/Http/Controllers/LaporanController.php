<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();
        $selectedMethod = $request->payment_method ?? 'all';

        // 2. Query Dasar (Filter Tanggal)
        $query = Sale::whereDate('created_at', '>=', $startDate)
                     ->whereDate('created_at', '<=', $endDate);

        // Filter Metode Pembayaran jika dipilih
        if ($selectedMethod != 'all') {
            if ($selectedMethod == 'utang') {
                $query->whereIn('status', ['belum_lunas']);
            } else {
                $query->where('payment_method', $selectedMethod);
            }
        }
        
        // Ambil Data Terbaru
        $sales = $query->orderBy('created_at', 'desc')->get();

        // 3. PERHITUNGAN RINGKASAN (YANG DIPERBAIKI)

        // A. Total Uang Masuk (Cashflow Riil)
        // Hitung semua kolom 'bayar' (baik dari lunas maupun cicilan)
        // Kita hitung dari data yang difilter tanggalnya
        $totalPendapatan = Sale::whereDate('created_at', '>=', $startDate)
                               ->whereDate('created_at', '<=', $endDate)
                               ->sum('bayar'); 
        
        // B. Rincian Tunai & Online
        $totalTunai = Sale::whereDate('created_at', '>=', $startDate)
                          ->whereDate('created_at', '<=', $endDate)
                          ->where('payment_method', 'cash')
                          ->sum('bayar');

        $totalOnline = Sale::whereDate('created_at', '>=', $startDate)
                           ->whereDate('created_at', '<=', $endDate)
                           ->whereIn('payment_method', ['online', 'qris'])
                           ->sum('bayar');

        // C. Total Piutang (SISA HUTANG YANG BENAR)
        // Rumus Lama: sum('grand_total') -> Salah karena tidak menghitung cicilan
        // Rumus Baru: (grand_total - bayar) -> Benar, sisa yang belum dibayar
        $totalPiutang = Sale::whereDate('created_at', '>=', $startDate)
                            ->whereDate('created_at', '<=', $endDate)
                            ->where('status', 'belum_lunas')
                            ->get()
                            ->sum(function($sale) {
                                return $sale->grand_total - $sale->bayar;
                            });

        $totalTransaksi = $sales->count();

        return view('laporan.index', compact(
            'sales', 
            'startDate', 
            'endDate', 
            'selectedMethod',
            'totalPendapatan',
            'totalPiutang', // Sekarang isinya Sisa Hutang
            'totalTunai',
            'totalOnline',
            'totalTransaksi'
        ));
    }
}