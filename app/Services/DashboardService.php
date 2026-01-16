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
        // Set default dates if not provided
        $tanggalMulai = $tanggalMulai ?? Carbon::now()->startOfMonth()->toDateString();
        $tanggalAkhir = $tanggalAkhir ?? Carbon::now()->endOfMonth()->toDateString();
        
        // Get data from repository
        $summary = $this->dashboardRepo->getSummary($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        $marketplace = $this->dashboardRepo->getMarketplacePerformance(
            $idPerusahaan, 
            Carbon::now()->year, 
            Carbon::now()->month
        );
        $topProducts = $this->dashboardRepo->getTopProducts($idPerusahaan, $tanggalMulai, $tanggalAkhir, 10);
        $salesTrend = $this->dashboardRepo->getSalesTrend($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        
        // Transform data for view
        return [
            'summary' => $this->formatSummary($summary),
            'marketplace' => $this->formatMarketplace($marketplace),
            'top_products' => $topProducts,
            'chart_data' => $this->prepareChartData($salesTrend),
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
                'total_pendapatan' => 0,
                'total_item' => 0,
                'rata_rata_order' => 0
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
                'nama' => $item->nama_marketplace,
                'total_order' => $item->total_order,
                'pendapatan' => $item->pendapatan_bersih,
                'pendapatan_formatted' => 'Rp ' . number_format($item->pendapatan_bersih, 0, ',', '.'),
                'margin' => $item->profit_margin_persen ?? 0
            ];
        }, $data);
    }
    
    /**
     * Prepare data for charts
     */
    private function prepareChartData(array $salesTrend): array
    {
        $labels = [];
        $data = [];
        
        foreach ($salesTrend as $item) {
            $labels[] = Carbon::parse($item->tanggal)->format('d M');
            $data[] = $item->pendapatan;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Pendapatan Harian',
                'data' => $data,
                'borderColor' => 'rgb(75, 192, 192)',
                'tension' => 0.1
            ]]
        ];
    }
}