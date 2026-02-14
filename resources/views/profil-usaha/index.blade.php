@extends('layouts.app')

@section('title', 'Profil Usaha')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Profil Usaha</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola informasi profil usaha Anda</p>
            </div>
            @if($profil)
                <a href="{{ route('profil-usaha.edit') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profil
                </a>
            @else
                <a href="{{ route('profil-usaha.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Profil
                </a>
            @endif
        </div>
    </div>

    @if($profil)
        <!-- Profil Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Logo Section -->
                    <div class="flex-shrink-0">
                        <div class="w-48 h-48 bg-gray-100 rounded-lg border-2 border-gray-200 flex items-center justify-center overflow-hidden">
                            @if($profil->logo_url)
                                <img src="{{ $profil->logo_url }}" 
                                     alt="Logo {{ $profil->nama_perusahaan }}" 
                                     class="w-full h-full object-contain">
                            @else
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-xs text-gray-500">Tidak ada logo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="flex-1">
                        <div class="space-y-4">
                            <!-- Nama Perusahaan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Nama Perusahaan
                                </label>
                                <p class="text-xl font-bold text-gray-900">{{ $profil->nama_perusahaan }}</p>
                            </div>

                            <!-- Bidang Usaha (Dropdown value) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Bidang Usaha
                                </label>
                                <p class="text-base text-gray-900">{{ $profil->bidang_usaha }}</p>
                            </div>

                            <!-- ✅ Jenis Usaha (Text input value) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    Jenis Usaha
                                </label>
                                <p class="text-base text-gray-900">{{ $profil->jenis_usaha ?? '-' }}</p>
                            </div>

                            <!-- Timestamp -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">
                                            Dibuat Pada
                                        </label>
                                        <p class="text-gray-700">
                                            {{ \Carbon\Carbon::parse($profil->dibuat_pada)->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">
                                            Diperbarui Pada
                                        </label>
                                        <p class="text-gray-700">
                                            {{ \Carbon\Carbon::parse($profil->diperbarui_pada)->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Status: <span class="font-medium text-green-600">Aktif</span></span>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12">
            <div class="text-center">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum ada profil usaha</h3>
                <p class="mt-2 text-sm text-gray-500">Mulai dengan membuat profil usaha Anda.</p>
                <div class="mt-6">
                    <a href="{{ route('profil-usaha.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Profil Usaha
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection