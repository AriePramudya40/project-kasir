<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak.');
        }

        // 1. Ambil Filter dari Request
        $startDate = $request->start_date ?? Carbon::today()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::today()->format('Y-m-d');
        $selectedMethod = $request->payment_method ?? 'all'; // Default: Tampilkan Semua

        // 2. Query Dasar (Hanya Filter Tanggal & Status) - Untuk Perhitungan Ringkasan
        // Kita pakai variabel terpisah agar Ringkasan tetap menghitung TOTAL periode tsb,
        // meskipun tabel di bawah sedang difilter "Tunai" saja.
        $allSalesInPeriod = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', '!=', 'batal')
            ->get();

        // 3. Hitung Ringkasan (Dari data periode)
        $totalTunai = $allSalesInPeriod->where('payment_method', 'cash')->sum('grand_total');
        $totalOnline = $allSalesInPeriod->whereIn('payment_method', ['online', 'qris', 'transfer'])->sum('grand_total');
        $totalPiutang = $allSalesInPeriod->where('payment_method', 'utang')->sum('grand_total');
        
        $totalPendapatan = $totalTunai + $totalOnline; // Uang Masuk (Cash + Online)
        $totalTransaksi = $allSalesInPeriod->count();

        // 4. Query Data Tabel (Bisa Difilter Metode Pembayaran)
        $query = Sale::with('user')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', '!=', 'batal')
            ->orderBy('created_at', 'desc');

        // Jika user memilih filter tertentu (bukan 'all'), kita tambahkan where
        if ($selectedMethod !== 'all') {
            if ($selectedMethod == 'online') {
                $query->whereIn('payment_method', ['online', 'qris', 'transfer']);
            } else {
                $query->where('payment_method', $selectedMethod);
            }
        }

        $sales = $query->get();

        return view('laporan.index', compact(
            'sales',
            'totalPendapatan', 'totalPiutang', 'totalTransaksi', 
            'totalTunai', 'totalOnline',
            'startDate', 'endDate', 'selectedMethod'
        ));
    }
}