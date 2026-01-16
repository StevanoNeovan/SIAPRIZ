<?php
// app/Services/DashboardService.php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Carbon\Carbon;

class DashboardService
{
    protected $dashboardRepo;
    
    public function __construct(DashboardRepositoryInterface $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }
    
    /**
     * Get complete dashboard data
     */
    public function getDashboardData(int $idPerusahaan, ?string $tanggalMulai = null, ?string $tanggalAkhir = null): array
    {
        // Set default dates if not provided (current month)
        $tanggalMulai = $tanggalMulai ?? Carbon::now()->startOfMonth()->toDateString();
        $tanggalAkhir = $tanggalAkhir ?? Carbon::now()->endOfMonth()->toDateString();
        
        // Get data from repository
        $summary = $this->dashboardRepo->getSummary($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        
        $marketplace = $this->dashboardRepo->getMarketplacePerformance(
            $idPerusahaan, 
            Carbon::parse($tanggalMulai)->year, 
            Carbon::parse($tanggalMulai)->month
        );
        
        $topProducts = $this->dashboardRepo->getTopProducts($idPerusahaan, $tanggalMulai, $tanggalAkhir, 10);
        
        $salesTrend = $this->dashboardRepo->getSalesTrend($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        
        $productPerMarketplace = $this->dashboardRepo->getProductPerformancePerMarketplace($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        
        // Transform data for view
        return [
            'summary' => $this->formatSummary($summary),
            'marketplace' => $this->formatMarketplace($marketplace),
            'top_products' => $topProducts,
            'product_per_marketplace' => $this->groupProductsByMarketplace($productPerMarketplace),
            'chart_data' => $this->prepareChartData($salesTrend),
            'marketplace_chart_data' => $this->prepareMarketplaceChartData($marketplace),
            'periode' => [
                'mulai' => $tanggalMulai,
                'akhir' => $tanggalAkhir
            ]
        ];
    }
    
    /**
     * Format summary data
     */
    private function formatSummary($summary): array
    {
        if (!$summary) {
            return [
                'total_order' => 0,
                'total_pendapatan' => '0',
                'total_item' => 0,
                'rata_rata_order' => '0'
            ];
        }
        
        return [
            'total_order' => $summary->total_order ?? 0,
            'total_pendapatan' => number_format($summary->pendapatan_bersih ?? 0, 0, ',', '.'),
            'total_item' => $summary->total_item_terjual ?? 0,
            'rata_rata_order' => number_format($summary->rata_rata_nilai_order ?? 0, 0, ',', '.'),
        ];
    }
    
    /**
     * Format marketplace data
     */
    private function formatMarketplace(array $data): array
    {
        return array_map(function($item) {
            return [
                'nama' => $item->nama_marketplace ?? 'Unknown',
                'total_order' => $item->total_order ?? 0,
                'item_terjual' => $item->item_terjual ?? 0,
                'pendapatan_kotor' => $item->pendapatan_kotor ?? 0,
                'pendapatan_bersih' => $item->pendapatan_bersih ?? 0,
                'pendapatan_formatted' => 'Rp ' . number_format($item->pendapatan_bersih ?? 0, 0, ',', '.'),
                'komisi' => $item->komisi_dibayar ?? 0,
                'margin' => number_format($item->profit_margin_persen ?? 0, 2)
            ];
        }, $data);
    }
    
    /**
     * Group products by marketplace for requirement 2
     */
    private function groupProductsByMarketplace(array $data): array
    {
        $grouped = [];
        
        foreach ($data as $item) {
            $marketplace = $item->nama_marketplace ?? 'Unknown';
            
            if (!isset($grouped[$marketplace])) {
                $grouped[$marketplace] = [];
            }
            
            $grouped[$marketplace][] = [
                'id_produk' => $item->id_produk ?? 0,
                'nama_produk' => $item->nama_produk ?? 'Unknown',
                'total_terjual' => $item->total_terjual ?? 0,
                'total_pendapatan' => $item->total_pendapatan ?? 0,
                'total_pendapatan_formatted' => 'Rp ' . number_format($item->total_pendapatan ?? 0, 0, ',', '.'),
                'jumlah_order' => $item->jumlah_order ?? 0,
            ];
        }
        
        return $grouped;
    }
    
    /**
     * Prepare data for sales trend line chart
     */
    private function prepareChartData(array $salesTrend): array
        {
            $labels = [];
            $pendapatan = [];
            $totalOrder = [];

            foreach ($salesTrend as $item) {
                $labels[] = Carbon::parse($item->tanggal)->format('d M');
                $pendapatan[] = $item->pendapatan ?? 0;
                $totalOrder[] = $item->jumlah_order ?? 0; 
            }

            return [
                'labels' => $labels,
                'pendapatan' => $pendapatan,
                'total_order' => $totalOrder
            ];
        }

    
    /**
     * Prepare data for marketplace bar chart
     */
    private function prepareMarketplaceChartData(array $marketplace): array
        {
            $labels = [];
            $pendapatan = [];
            $totalOrder = [];
            $colors = [];

             $marketplaceColors = [
            'Shopee'    => 'rgba(238, 77, 45, 0.85)',   
            'Tokopedia' => 'rgba(3, 172, 14, 0.85)',    
            'Lazada'    => 'rgba(65, 105, 225, 0.85)',  
            'Umum'      => 'rgba(107, 114, 128, 0.85)',
        ];

            foreach ($marketplace as $item) {
                $nama = $item->nama_marketplace ?? 'Unknown';

                $labels[] = $nama;
                $pendapatan[] = $item->pendapatan_kotor ?? 0;
                $totalOrder[] = $item->total_order ?? 0;
                $colors[] = $marketplaceColors[$nama] ?? '#9CA3AF';
            }

            return [
                'labels' => $labels,
                'pendapatan' => $pendapatan,
                'total_order' => $totalOrder,
                'colors' => $colors
            ];
        }

}