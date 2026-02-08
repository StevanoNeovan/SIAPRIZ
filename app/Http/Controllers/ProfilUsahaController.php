<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfilUsahaController extends Controller
{
    /**
     * Display profil usaha
     */
    public function index()
    {
        // Ambil profil perusahaan pertama (asumsi single company)
        $profil = Auth::user()->perusahaan;

        return view('profil-usaha.index', compact('profil'));
    }

    /**
     * Show the form for creating a new profil usaha
     */
    public function create()
{
    $profil = Auth::user()->perusahaan;

    if ($profil) {
        return redirect()->route('profil-usaha.index')
            ->with('error', 'Profil usaha sudah ada. Silakan edit.');
    }

    return view('profil-usaha.create');
}


    /**
     * Store a newly created profil usaha in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'bidang_usaha.required' => 'Bidang usaha wajib diisi',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Logo harus berformat: jpeg, png, jpg, atau gif',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ]);

        // Handle logo upload
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $logoUrl = Storage::url($logoPath);
        }

        // Create profil perusahaan
        Perusahaan::create([
            'id_perusahaan' => Auth::user()->id_perusahaan,
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'bidang_usaha' => $validated['bidang_usaha'],
            'logo_url' => $logoUrl,
            'is_aktif' => true,
        ]);

        return redirect()->route('profil-usaha.index')
            ->with('success', 'Profil usaha berhasil dibuat!');
    }

    /**
     * Show the form for editing the profil usaha
     */
    public function edit()
    {
        $profil = Auth::user()->perusahaan;

        if (!$profil) {
            return redirect()->route('profil-usaha.create')
                ->with('error', 'Profil usaha belum ada. Silakan buat profil terlebih dahulu.');
        }

        return view('profil-usaha.edit', compact('profil'));
    }

    /**
     * Update the profil usaha in storage
     */
    public function update(Request $request)
    {
        $profil = Auth::user()->perusahaan;

        if (!$profil) {
            return redirect()->route('profil-usaha.create')
                ->with('error', 'Profil usaha tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'bidang_usaha.required' => 'Bidang usaha wajib diisi',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Logo harus berformat: jpeg, png, jpg, atau gif',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($profil->logo_url) {
                $oldLogoPath = str_replace('/storage/', '', $profil->logo_url);
                Storage::disk('public')->delete($oldLogoPath);
            }

            // Upload new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_url'] = Storage::url($logoPath);
        }

        // Update profil
        $profil->update($validated);

        return redirect()->route('profil-usaha.index')
            ->with('success', 'Profil usaha berhasil diperbarui!');
    }

    /**
     * Remove logo from profil usaha
     */
    public function removeLogo()
    {
        $profil = Auth::user()->perusahaan;

        if (!$profil) {
            return redirect()->route('profil-usaha.index')
                ->with('error', 'Profil usaha tidak ditemukan.');
        }

        // Delete logo file
        if ($profil->logo_url) {
            $logoPath = str_replace('/storage/', '', $profil->logo_url);
            Storage::disk('public')->delete($logoPath);
            
            $profil->update(['logo_url' => null]);
        }

        return redirect()->route('profil-usaha.edit')
            ->with('success', 'Logo berhasil dihapus!');
    }
}