<?php
// app/Repositories/Eloquent/DashboardRepository.php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get dashboard summary via stored procedure
     */
    public function getSummary(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): ?object
    {
        try {
            $result = DB::select('CALL sp_ambil_data_dashboard(?, ?, ?)', [
                $idPerusahaan,
                $tanggalMulai,
                $tanggalAkhir
            ]);

            return $result[0] ?? null;
        } catch (\Exception $e) {
            \Log::error('Error in getSummary: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get marketplace performance comparison
     */
    public function getMarketplacePerformance(int $idPerusahaan, int $tahun, int $bulan): array
    {
        try {
            $results = DB::select('CALL sp_perbandingan_marketplace(?, ?, ?)', [
                $idPerusahaan,
                $tahun,
                $bulan
            ]);
            
            return $results ?? [];
        } catch (\Exception $e) {
            Log::error('Error in getMarketplacePerformance: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get top selling products for a period
     */
    public function getTopProducts(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir, int $limit = 10): array
    {
        try {
            $results = DB::select('CALL sp_produk_terlaris_per_periode(?, ?, ?, ?)', [
                $idPerusahaan,
                $tanggalMulai,
                $tanggalAkhir,
                $limit
            ]);
            
            return $results ?? [];
        } catch (\Exception $e) {
            Log::error('Error in getTopProducts: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product performance per marketplace
     */
    public function getProductPerformancePerMarketplace(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): array
    {
        try {
            $results = DB::select('CALL sp_kinerja_produk_per_marketplace(?, ?, ?)', [
                $idPerusahaan,
                $tanggalMulai,
                $tanggalAkhir
            ]);
            
            return $results ?? [];
        } catch (\Exception $e) {
            Log::error('Error in getProductPerformancePerMarketplace: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get sales trend for charts (daily aggregation)
     * UPDATED: menggunakan total_pesanan bukan pendapatan_bersih
     */
    public function getSalesTrend(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): array
    {
        try {
            $results = DB::table('penjualan_transaksi')
                ->select(
                    DB::raw('DATE(tanggal_order) as tanggal'),
                    DB::raw('SUM(total_pesanan) as pendapatan'),
                    DB::raw('COUNT(DISTINCT id_transaksi) as jumlah_order')
                )
                ->where('id_perusahaan', $idPerusahaan)
                ->whereBetween('tanggal_order', [$tanggalMulai, $tanggalAkhir])
                ->where('status_order', 'selesai')
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get()
                ->toArray();
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Error in getSalesTrend: ' . $e->getMessage());
            return [];
        }
    }
}