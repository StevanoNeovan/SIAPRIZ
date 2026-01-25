@extends('layouts.app')

@section('title', 'Buat Profil Usaha')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Buat Profil Usaha</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi informasi profil usaha Anda</p>
        </div>
    </div>

    <!-- Validation Error -->
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"/>
                </svg>
                <div class="ml-3 text-sm text-red-800 space-y-1">
                    @foreach ($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ======================
         FORM CREATE PROFIL
    ======================= -->
    <form action="{{ route('profil-usaha.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white rounded-lg shadow">
        @csrf

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Logo Perusahaan
                    </label>

                    <div class="w-full h-48 bg-gray-100 rounded-lg border-2 border-gray-200 flex items-center justify-center overflow-hidden">
                        <div class="text-center text-gray-400">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-xs">Belum ada logo</p>
                        </div>
                    </div>

                    <input type="file"
                           name="logo"
                           class="mt-3 text-sm w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Data -->
                <div class="md:col-span-2 space-y-5">

                    <!-- Nama Perusahaan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Perusahaan
                        </label>
                        <input type="text"
                               name="nama_perusahaan"
                               value="{{ old('nama_perusahaan') }}"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500"
                               placeholder="Masukkan nama perusahaan">
                    </div>

                    <!-- Bidang Usaha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bidang Usaha
                        </label>
                        <input type="text"
                               name="bidang_usaha"
                               value="{{ old('bidang_usaha') }}"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-indigo-500"
                               placeholder="Contoh: Kuliner, Retail, Jasa">
                    </div>

                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t border-gray-200">
            <a href="{{ route('profil-usaha.index') }}"
               class="px-4 py-2 border rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                Batal
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                Simpan Profil
            </button>
        </div>
    </form>

</div>
@endsection
