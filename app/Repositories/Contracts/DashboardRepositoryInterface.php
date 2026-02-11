<?php
// app/Repositories/Contracts/DashboardRepositoryInterface.php

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    /**
     * Get dashboard summary data
     * 
     * @param int $idPerusahaan
     * @param string $tanggalMulai
     * @param string $tanggalAkhir
     * @return object|null
     */
    public function getSummary(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): ?object;
    
    /**
     * Get sales performance by marketplace
     * 
     * @param int $idPerusahaan
     * @param int $tahun
     * @param int $bulan
     * @return array
     */
    public function getMarketplacePerformance(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): array;
    
    /**
     * Get top selling products
     * 
     * @param int $idPerusahaan
     * @param string $tanggalMulai
     * @param string $tanggalAkhir
     * @param int $limit
     * @return array
     */
    public function getTopProducts(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir, int $limit = 10): array;
    
    /**
     * Get sales trend data for charts
     * 
     * @param int $idPerusahaan
     * @param string $tanggalMulai
     * @param string $tanggalAkhir
     * @return array
     */
    public function getSalesTrend(int $idPerusahaan, string $tanggalMulai, string $tanggalAkhir): array;
}