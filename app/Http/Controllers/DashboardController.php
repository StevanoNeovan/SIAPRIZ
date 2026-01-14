<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
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
        
        return view('dashboard.index', $data);
    }
}