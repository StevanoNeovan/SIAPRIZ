<?php
// app/Http/Controllers/UploadPenjualanController.php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPenjualanRequest;
use App\Services\UploadService;
use App\Models\Marketplace;
use Illuminate\Http\Request;

class UploadPenjualanController extends Controller
{
    protected $uploadService;
    
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
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
     * Process file upload
     */
    public function store(UploadPenjualanRequest $request)
    {
        $result = $this->uploadService->processUpload(
            $request->file('file'),
            auth()->user()->id_perusahaan,
            auth()->user()->id_pengguna,
            $request->input('id_marketplace')
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
}