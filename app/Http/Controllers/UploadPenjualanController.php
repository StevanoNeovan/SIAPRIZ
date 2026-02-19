<?php
// app/Http/Controllers/UploadPenjualanController.php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPenjualanRequest;
use App\Services\UploadService;
use App\Services\TemplateService;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadPenjualanController extends Controller
{
    protected $uploadService;
    protected $templateService;
    
    public function __construct(UploadService $uploadService, TemplateService $templateService)
    {
        $this->uploadService = $uploadService;
        $this->templateService = $templateService;
    }
    
    /**
     * Show upload form
     */
    public function index()
    {
        $marketplaces = Marketplace::getAllActive();
        $history = $this->uploadService->getUploadHistory(
            auth()->user()->id_perusahaan,
            10
        );
        
        return view('penjualan.upload', [
            'marketplaces' => $marketplaces,
            'history' => $history,
        ]);
    }
    
    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $filepath = $this->templateService->generateTemplate();
        
        return response()->download($filepath)->deleteFileAfterSend(true);
    }
    
    /**
     * Process file upload
     */
    public function store(UploadPenjualanRequest $request)
    {
        if (!$request->id_marketplace) {
            return redirect()
                ->route('penjualan.upload')
                ->with('error', 'Silakan pilih marketplace terlebih dahulu.');
        }
        
        // Determine upload type: template (true) or direct marketplace CSV (false)
        $useTemplate = $request->input('upload_type', 'template') === 'template';
        
        $result = $this->uploadService->processUpload(
            $request->file('file'),
            auth()->user()->id_perusahaan,
            auth()->user()->id_pengguna,
            $request->input('id_marketplace'),
            $useTemplate
        );
        
        if ($result['success']) {
            return redirect()
                ->route('penjualan.upload')
                ->with('success', "Berhasil mengupload {$result['total_orders']} transaksi. " . 
                    ($result['total_failed'] > 0 ? "{$result['total_failed']} gagal diproses." : ''));
        }
        
        return redirect()
            ->route('penjualan.upload')
            ->with('error', $result['error']);
    }
    
    /**
     * Show upload history detail
     */
    public function show(int $id)
    {
        $log = \App\Models\LogUpload::with(['marketplace', 'pengguna', 'transaksi'])
            ->where('id_perusahaan', auth()->user()->id_perusahaan)
            ->findOrFail($id);
        
        return view('penjualan.upload-detail', [
            'log' => $log,
        ]);
    }
    
    /**
     * Download uploaded file
     */
    public function downloadFile(int $id)
    {
        $log = \App\Models\LogUpload::where('id_perusahaan', auth()->user()->id_perusahaan)
            ->findOrFail($id);
        
        if (!$log->hasFile()) {
            return redirect()
                ->route('penjualan.upload')
                ->with('error', 'File tidak ditemukan.');
        }
        
        return Storage::disk('public')->download($log->file_path, $log->nama_file);
    }

     /**
     * Delete upload log AND semua transaksi terkait
     * Route: DELETE /penjualan/upload/{id}
     */
    public function destroy(int $id)
    {
        $log = \App\Models\LogUpload::where('id_perusahaan', auth()->user()->id_perusahaan)
            ->findOrFail($id);

        // 1. Hapus file fisik jika ada
        if ($log->hasFile()) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($log->file_path);
        }

        // 2. Hapus semua penjualan_transaksi yang terkait batch ini
        //    (penjualan_transaksi_detail terhapus otomatis via ON DELETE CASCADE)
        \App\Models\PenjualanTransaksi::where('id_batch_upload', $id)->delete();

        // 3. Hapus log upload
        $log->delete();

        return redirect()
            ->route('penjualan.upload')
            ->with('success', 'Upload beserta data transaksinya berhasil dihapus.');
    }
}