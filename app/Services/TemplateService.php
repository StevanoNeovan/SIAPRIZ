<?php
// app/Services/TemplateService.php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * UPDATED: Template Excel yang disederhanakan
 * Hapus kolom: Biaya Komisi, Pendapatan Bersih
 */
class TemplateService
{
    /**
     * Generate Excel template for upload
     */
    public function generateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Define columns - UPDATED: Hapus Biaya Komisi & Pendapatan Bersih
        $columns = [
            'A' => 'No. Pesanan',
            'B' => 'Tanggal Order',
            'C' => 'Status Order',
            'D' => 'SKU',
            'E' => 'Nama Produk',
            'F' => 'Variasi',
            'G' => 'Jumlah',
            'H' => 'Harga Satuan',
            'I' => 'Total Pesanan',
            'J' => 'Total Diskon',
            'K' => 'Ongkos Kirim',
            'L' => 'Nama Customer',
            'M' => 'Kota',
            'N' => 'Provinsi',
        ];
        
        // Set header
        foreach ($columns as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getColumnDimension($col)->setWidth(15);
        }
        
        // Add example data (row 2)
        $sheet->setCellValue('A2', 'ORDER-001');
        $sheet->setCellValue('B2', '2026-01-17');
        $sheet->setCellValue('C2', 'selesai');
        $sheet->setCellValue('D2', 'SKU-001');
        $sheet->setCellValue('E2', 'Contoh Produk 1');
        $sheet->setCellValue('F2', 'Merah, L');
        $sheet->setCellValue('G2', 2);
        $sheet->setCellValue('H2', 150000);
        $sheet->setCellValue('I2', 300000);
        $sheet->setCellValue('J2', 30000);
        $sheet->setCellValue('K2', 15000);
        $sheet->setCellValue('L2', 'John Doe');
        $sheet->setCellValue('M2', 'Surabaya');
        $sheet->setCellValue('N2', 'Jawa Timur');
        
        // Add second product in same order
        $sheet->setCellValue('A3', 'ORDER-001');
        $sheet->setCellValue('B3', '2026-01-17');
        $sheet->setCellValue('C3', 'selesai');
        $sheet->setCellValue('D3', 'SKU-002');
        $sheet->setCellValue('E3', 'Contoh Produk 2');
        $sheet->setCellValue('F3', 'Biru, M');
        $sheet->setCellValue('G3', 1);
        $sheet->setCellValue('H3', 100000);
        $sheet->setCellValue('I3', '');
        $sheet->setCellValue('J3', '');
        $sheet->setCellValue('K3', '');
        $sheet->setCellValue('L3', '');
        $sheet->setCellValue('M3', '');
        $sheet->setCellValue('N3', '');
        
        // Style example rows
        $sheet->getStyle('A2:N3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
        ]);
        
        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instruksi');
        $instructionSheet->setCellValue('A1', 'INSTRUKSI PENGGUNAAN TEMPLATE');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $instructions = [
            '',
            '1. KOLOM WAJIB DIISI:',
            '   - No. Pesanan: ID unik order dari marketplace',
            '   - Tanggal Order: Format YYYY-MM-DD (contoh: 2026-01-17)',
            '   - Status Order: selesai / proses / dibatalkan / dikembalikan',
            '   - SKU: Kode produk',
            '   - Nama Produk: Nama produk',
            '   - Jumlah: Quantity produk',
            '   - Harga Satuan: Harga per item',
            '   - Total Pesanan: Total pembayaran customer (pendapatan kotor)',
            '',
            '2. UNTUK ORDER DENGAN BANYAK PRODUK:',
            '   - Tulis No. Pesanan yang sama di baris berikutnya',
            '   - Isi data finansial (Total Pesanan, Diskon, dll) hanya di BARIS PERTAMA order',
            '   - Baris produk selanjutnya kosongkan kolom finansial',
            '',
            '3. STATUS ORDER:',
            '   - selesai: Order sudah dikirim & diterima',
            '   - proses: Order sedang diproses',
            '   - dibatalkan: Order dibatalkan',
            '   - dikembalikan: Order dikembalikan/refund',
            '',
            '4. KOLOM OPSIONAL:',
            '   - Variasi: Varian produk (boleh kosong)',
            '   - Total Diskon: Boleh kosong (default 0)',
            '   - Ongkos Kirim: Boleh kosong (default 0)',
            '   - Nama Customer, Kota, Provinsi: Boleh kosong',
            '',
            '5. CATATAN PENTING:',
            '   - Total Pesanan = Pendapatan Kotor (yang dibayar customer)',
            '   - Biaya komisi marketplace TIDAK perlu diisi',
            '   - Sistem akan menghitung pendapatan berdasarkan Total Pesanan',
            '',
            '6. SETELAH SELESAI MENGISI:',
            '   - Hapus baris contoh (baris 2 & 3)',
            '   - Hapus sheet Instruksi ini',
            '   - Upload file Excel',
        ];
        
        $row = 2;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction);
            $row++;
        }
        
        $instructionSheet->getColumnDimension('A')->setWidth(80);
        
        // Save to temp file
        $filename = 'template_upload_siapriz_' . date('Ymd') . '.xlsx';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        
        return $filepath;
    }
}