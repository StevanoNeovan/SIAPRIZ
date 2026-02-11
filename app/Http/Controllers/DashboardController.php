<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $reportService;
    
    public function __construct(
        DashboardService $dashboardService,
        ReportService $reportService
    ) {
        $this->dashboardService = $dashboardService;
        $this->reportService = $reportService;
    }
    
    /**
     * Display dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $data = $this->dashboardService->getDashboardData(
            $user->id_perusahaan,
            $request->tanggal_mulai,
            $request->tanggal_akhir
        );
        
        return view('dashboard', $data);
    }
    
    /**
     * Show product transaction details
     * Route: GET /dashboard/product/{id}/details
     */
    public function showProductDetails(Request $request, int $idProduk)
    {
        $user = auth()->user();
        
        $details = $this->dashboardService->getProductDetails(
            $user->id_perusahaan,
            $idProduk,
            $request->tanggal_mulai,
            $request->tanggal_akhir
        );
        
        return response()->json([
            'success' => true,
            'data' => $details
        ]);
    }
    
    /**
     * NEW: Download complete dashboard report as Excel
     * Route: GET /dashboard/download-report
     */
    public function downloadReport(Request $request)
    {
        $user = auth()->user();
        
        try {
            $filepath = $this->reportService->generateDashboardReport(
                $user->id_perusahaan,
                $user->perusahaan->nama_perusahaan,
                $request->tanggal_mulai,
                $request->tanggal_akhir
            );
            
            return response()->download($filepath)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            \Log::error('Error generating report: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal membuat laporan. Silakan coba lagi.');
        }
    }
}