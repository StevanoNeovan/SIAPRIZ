<?php
// app/Services/ReportService.php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class ReportService
{
    protected $dashboardRepo;
    
    public function __construct(DashboardRepositoryInterface $dashboardRepo)
    {
        $this->dashboardRepo = $dashboardRepo;
    }
    
    /**
     * Generate complete dashboard report in Excel format
     */
    public function generateDashboardReport(
        int $idPerusahaan,
        string $namaPerusahaan,
        ?string $tanggalMulai = null,
        ?string $tanggalAkhir = null
    ): string {
        // Set default dates
        $tanggalMulai = $tanggalMulai ?? Carbon::now()->startOfMonth()->toDateString();
        $tanggalAkhir = $tanggalAkhir ?? Carbon::now()->endOfMonth()->toDateString();
        
        // Get all data
        $summary = $this->dashboardRepo->getSummary($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        $marketplace = $this->dashboardRepo->getMarketplacePerformance(
            $idPerusahaan,
            Carbon::parse($tanggalMulai)->year,
            Carbon::parse($tanggalMulai)->month
        );
        $topProducts = $this->dashboardRepo->getTopProducts($idPerusahaan, $tanggalMulai, $tanggalAkhir, 20);
        $productPerMarketplace = $this->dashboardRepo->getProductPerformancePerMarketplace(
            $idPerusahaan,
            $tanggalMulai,
            $tanggalAkhir
        );
        $salesTrend = $this->dashboardRepo->getSalesTrend($idPerusahaan, $tanggalMulai, $tanggalAkhir);
        
        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        
        // 1. Cover Sheet
        $this->createCoverSheet($spreadsheet, $namaPerusahaan, $tanggalMulai, $tanggalAkhir, $summary);
        
        // 2. Summary Sheet
        $this->createSummarySheet($spreadsheet, $summary, $tanggalMulai, $tanggalAkhir);
        
        // 3. Marketplace Performance Sheet
        $this->createMarketplaceSheet($spreadsheet, $marketplace);
        
        // 4. Top Products Sheet
        $this->createTopProductsSheet($spreadsheet, $topProducts);
        
        // 5. Product Per Marketplace Sheet
        $this->createProductPerMarketplaceSheet($spreadsheet, $productPerMarketplace);
        
        // 6. Sales Trend Sheet
        $this->createSalesTrendSheet($spreadsheet, $salesTrend);
        
        // Set active sheet to cover
        $spreadsheet->setActiveSheetIndex(0);
        
        // Save to file
        $filename = 'Laporan_Dashboard_' . $namaPerusahaan . '_' . 
                    Carbon::parse($tanggalMulai)->format('d-M-Y') . '_sd_' . 
                    Carbon::parse($tanggalAkhir)->format('d-M-Y') . '.xlsx';
        
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return $filepath;
    }
    
    /**
     * Create cover sheet
     */
    private function createCoverSheet(Spreadsheet $spreadsheet, string $namaPerusahaan, string $tanggalMulai, string $tanggalAkhir, $summary)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cover');
        
        // Company name
        $sheet->setCellValue('A2', 'LAPORAN DASHBOARD PENJUALAN');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        $sheet->setCellValue('A3', $namaPerusahaan);
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Period
        $sheet->setCellValue('A5', 'Periode:');
        $sheet->setCellValue('B5', Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d F Y'));
        $sheet->getStyle('A5')->getFont()->setBold(true);
        
        // Summary cards
        $sheet->setCellValue('A7', 'RINGKASAN');
        $sheet->mergeCells('A7:E7');
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $row = 9;
        $summaryData = [
            ['Metric', 'Nilai'],
            ['Total Order', number_format($summary->total_order ?? 0)],
            ['Total Pendapatan', 'Rp ' . number_format($summary->total_pendapatan ?? 0, 0, ',', '.')],
            ['Total Item Terjual', number_format($summary->total_item_terjual ?? 0)],
            ['Rata-rata Nilai Order', 'Rp ' . number_format($summary->rata_rata_nilai_order ?? 0, 0, ',', '.')],
        ];
        
        foreach ($summaryData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $row++;
        }
        
        // Style summary table
        $sheet->getStyle('A9:B9')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $sheet->getStyle('A9:B' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
            ],
        ]);
        
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        
        // Generated info
        $sheet->setCellValue('A' . ($row + 2), 'Laporan dibuat pada: ' . Carbon::now()->format('d F Y H:i:s'));
        $sheet->getStyle('A' . ($row + 2))->getFont()->setItalic(true)->setSize(9);
    }
    
    /**
     * Create summary sheet
     */
    private function createSummarySheet(Spreadsheet $spreadsheet, $summary, string $tanggalMulai, string $tanggalAkhir)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Ringkasan');
        
        // Title
        $sheet->setCellValue('A1', 'RINGKASAN DASHBOARD');
        $sheet->mergeCells('A1:D1');
        $this->styleHeader($sheet, 'A1:D1');
        
        $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($tanggalMulai)->format('d M Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d M Y'));
        
        // Data
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Metrik');
        $sheet->setCellValue('B' . $row, 'Nilai');
        $this->styleTableHeader($sheet, 'A' . $row . ':B' . $row);
        
        $row++;
        $data = [
            ['Total Order', number_format($summary->total_order ?? 0)],
            ['Total Pendapatan', 'Rp ' . number_format($summary->total_pendapatan ?? 0, 0, ',', '.')],
            ['Total Item Terjual', number_format($summary->total_item_terjual ?? 0)],
            ['Produk Unik', number_format($summary->produk_unik ?? 0)],
            ['Rata-rata Nilai Order', 'Rp ' . number_format($summary->rata_rata_nilai_order ?? 0, 0, ',', '.')],
        ];
        
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item[0]);
            $sheet->setCellValue('B' . $row, $item[1]);
            $row++;
        }
        
        $this->styleTable($sheet, 'A4:B' . ($row - 1));
        $this->autoSizeColumns($sheet, ['A', 'B']);
    }
    
    /**
     * Create marketplace performance sheet
     */
    private function createMarketplaceSheet(Spreadsheet $spreadsheet, array $marketplace)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Kinerja Marketplace');
        
        // Title
        $sheet->setCellValue('A1', 'KINERJA PENJUALAN PER MARKETPLACE');
        $sheet->mergeCells('A1:D1');
        $this->styleHeader($sheet, 'A1:D1');
        
        // Headers
        $row = 3;
        $headers = ['Marketplace', 'Total Order', 'Item Terjual', 'Pendapatan Kotor'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $this->styleTableHeader($sheet, 'A' . $row . ':D' . $row);
        
        // Data
        $row++;
        foreach ($marketplace as $mp) {
            $sheet->setCellValue('A' . $row, $mp->nama_marketplace ?? '-');
            $sheet->setCellValue('B' . $row, $mp->total_order ?? 0);
            $sheet->setCellValue('C' . $row, $mp->item_terjual ?? 0);
            $sheet->setCellValue('D' . $row, $mp->total_pendapatan ?? 0);
            
            // Format currency
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }
        
        // Total row
        if (!empty($marketplace)) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, '=SUM(B4:B' . ($row - 1) . ')');
            $sheet->setCellValue('C' . $row, '=SUM(C4:C' . ($row - 1) . ')');
            $sheet->setCellValue('D' . $row, '=SUM(D4:D' . ($row - 1) . ')');
            
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        $this->styleTable($sheet, 'A3:D' . $row);
        $this->autoSizeColumns($sheet, ['A', 'B', 'C', 'D']);
    }
    
    /**
     * Create top products sheet
     */
    private function createTopProductsSheet(Spreadsheet $spreadsheet, array $products)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Produk Terlaris');
        
        // Title
        $sheet->setCellValue('A1', 'PRODUK TERLARIS - SEMUA MARKETPLACE');
        $sheet->mergeCells('A1:F1');
        $this->styleHeader($sheet, 'A1:F1');
        
        // Headers
        $row = 3;
        $headers = ['Ranking', 'Nama Produk', 'Kategori', 'Total Terjual', 'Pendapatan', 'Jumlah Transaksi'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $this->styleTableHeader($sheet, 'A' . $row . ':F' . $row);
        
        // Data
        $row++;
        $ranking = 1;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $ranking);
            $sheet->setCellValue('B' . $row, $product->nama_produk ?? '-');
            $sheet->setCellValue('C' . $row, $product->kategori ?? '-');
            $sheet->setCellValue('D' . $row, $product->total_terjual ?? 0);
            $sheet->setCellValue('E' . $row, $product->total_pendapatan ?? 0);
            $sheet->setCellValue('F' . $row, $product->jumlah_transaksi ?? 0);
            
            // Format currency
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            // Highlight top 3
            if ($ranking <= 3) {
                $colors = ['FFD700', 'C0C0C0', 'CD7F32']; // Gold, Silver, Bronze
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $colors[$ranking - 1]]],
                    'font' => ['bold' => true],
                ]);
            }
            
            $row++;
            $ranking++;
        }
        
        $this->styleTable($sheet, 'A3:F' . ($row - 1));
        $this->autoSizeColumns($sheet, ['A', 'B', 'C', 'D', 'E', 'F']);
    }
    
    /**
     * Create product per marketplace sheet
     */
    private function createProductPerMarketplaceSheet(Spreadsheet $spreadsheet, array $data)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Produk Per Marketplace');
        
        // Title
        $sheet->setCellValue('A1', 'KINERJA PRODUK PER MARKETPLACE');
        $sheet->mergeCells('A1:E1');
        $this->styleHeader($sheet, 'A1:E1');
        
        // Headers
        $row = 3;
        $headers = ['Marketplace', 'Nama Produk', 'Total Terjual', 'Pendapatan', 'Jumlah Order'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $this->styleTableHeader($sheet, 'A' . $row . ':E' . $row);
        
        // Data
        $row++;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nama_marketplace ?? '-');
            $sheet->setCellValue('B' . $row, $item->nama_produk ?? '-');
            $sheet->setCellValue('C' . $row, $item->total_terjual ?? 0);
            $sheet->setCellValue('D' . $row, $item->total_pendapatan ?? 0);
            $sheet->setCellValue('E' . $row, $item->jumlah_order ?? 0);
            
            // Format currency
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }
        
        $this->styleTable($sheet, 'A3:E' . ($row - 1));
        $this->autoSizeColumns($sheet, ['A', 'B', 'C', 'D', 'E']);
    }
    
    /**
     * Create sales trend sheet
     */
    private function createSalesTrendSheet(Spreadsheet $spreadsheet, array $salesTrend)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tren Penjualan');
        
        // Title
        $sheet->setCellValue('A1', 'TREN PENJUALAN HARIAN');
        $sheet->mergeCells('A1:C1');
        $this->styleHeader($sheet, 'A1:C1');
        
        // Headers
        $row = 3;
        $headers = ['Tanggal', 'Pendapatan', 'Jumlah Order'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $this->styleTableHeader($sheet, 'A' . $row . ':C' . $row);
        
        // Data
        $row++;
        foreach ($salesTrend as $trend) {
            $sheet->setCellValue('A' . $row, Carbon::parse($trend->tanggal)->format('d M Y'));
            $sheet->setCellValue('B' . $row, $trend->pendapatan ?? 0);
            $sheet->setCellValue('C' . $row, $trend->jumlah_order ?? 0);
            
            // Format currency
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }
        
        // Total row
        if (!empty($salesTrend)) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->setCellValue('B' . $row, '=SUM(B4:B' . ($row - 1) . ')');
            $sheet->setCellValue('C' . $row, '=SUM(C4:C' . ($row - 1) . ')');
            
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            ]);
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0');
        }
        
        $this->styleTable($sheet, 'A3:C' . $row);
        $this->autoSizeColumns($sheet, ['A', 'B', 'C']);
    }
    
    /**
     * Style header
     */
    private function styleHeader($sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
    }
    
    /**
     * Style table header
     */
    private function styleTableHeader($sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
    
    /**
     * Style table
     */
    private function styleTable($sheet, string $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ]);
    }
    
    /**
     * Auto size columns
     */
    private function autoSizeColumns($sheet, array $columns)
    {
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}