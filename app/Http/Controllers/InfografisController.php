<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class InfografisController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display Infografis
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $data = $this->dashboardService->getDashboardData(
            $user->id_perusahaan,
            $request->tanggal_mulai,
            $request->tanggal_akhir
        );

        return view('infografis.index', $data);
    }
}
