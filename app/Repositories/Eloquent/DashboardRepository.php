<?php
// app/Repositories/Eloquent/DashboardRepository.php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get dashboard summary via stored procedure
     */
    public function getSummary(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): ?object
    {
        $result = DB::select('CALL sp_ambil_data_dashboard(?, ?, ?)', [
    $idPerusahaan,
    $tanggalMulai,
    $tanggalAkhir
    ]);

    $summary = $result[0];

    $totalOrder = $summary->total_order;
    $produkUnik = $summary->produk_unik;

    return $result[0] ?? null;

    }
    
    /**
     * Get marketplace performance comparison
     */
    public function getMarketplacePerformance(int $idPerusahaan, int $tahun, int $bulan): array
    {
        $results = DB::select('CALL sp_perbandingan_marketplace(?, ?, ?)', [
            $idPerusahaan,
            $tahun,
            $bulan
        ]);
        
        return $results ?? [];
    }
    
    /**
     * Get top selling products
     */
    public function getTopProducts(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir, int $limit = 10): array
    {
        // Using view instead of SP for this one
        return DB::table('v_produk_terlaris')
            ->where('id_perusahaan', $idPerusahaan)
            ->limit($limit)
            ->get()
            ->toArray();
    }
    
    /**
     * Get sales trend for charts
     */
    public function getSalesTrend(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): array
    {
        return DB::table('penjualan_transaksi')
            ->select(
                DB::raw('DATE(tanggal_order) as tanggal'),
                DB::raw('SUM(pendapatan_bersih) as pendapatan'),
                DB::raw('COUNT(*) as jumlah_order')
            )
            ->where('id_perusahaan', $idPerusahaan)
            ->whereBetween('tanggal_order', [$tanggalMulai, $tanggalAkhir])
            ->where('status_order', 'selesai')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->toArray();
    }
}