<!-- resources/views/penjualan/upload-detail.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Upload')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('penjualan.upload') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
            ← Kembali ke Upload
        </a>
    </div>

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Detail Upload</h2>
        <p class="mt-1 text-sm text-gray-600">{{ $log->nama_file }}</p>
    </div>

    <!-- Upload Info Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Informasi Upload</h3>
        </div>
        
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Upload</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->tanggal_upload->format('d F Y, H:i') }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Marketplace</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->marketplace->nama_marketplace }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Diupload Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->pengguna->nama_lengkap }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ukuran File</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($log->ukuran_file / 1024, 2) }} KB</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($log->status_upload === 'selesai')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Selesai
                            </span>
                        @elseif($log->status_upload === 'proses')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Proses
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Gagal
                            </span>
                        @endif
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Baris</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->total_baris }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Berhasil Diproses</dt>
                    <dd class="mt-1 text-sm text-green-600 font-semibold">{{ $log->baris_sukses }} transaksi</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Gagal Diproses</dt>
                    <dd class="mt-1 text-sm text-red-600 font-semibold">{{ $log->baris_gagal }} transaksi</dd>
                </div>
            </dl>
            
            @if($log->pesan_error)
                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Pesan Error</dt>
                    <dd class="text-sm text-red-600 bg-red-50 p-4 rounded border border-red-200">
                        <pre class="whitespace-pre-wrap">{{ $log->pesan_error }}</pre>
                    </dd>
                </div>
            @endif
        </div>
    </div>

    <!-- Transactions Table -->
    @if($log->transaksi->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">
                    Transaksi yang Diupload ({{ $log->transaksi->count() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Pesanan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendapatan Bersih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($log->transaksi->take(50) as $transaksi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $transaksi->order_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaksi->tanggal_order->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaksi->status_order === 'selesai')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @elseif($transaksi->status_order === 'proses')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Proses
                                        </span>
                                    @elseif($transaksi->status_order === 'dibatalkan')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Dikembalikan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    Rp {{ number_format($transaksi->total_pesanan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                    Rp {{ number_format($transaksi->pendapatan_bersih, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaksi->details->count() }} item
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($log->transaksi->count() > 50)
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-sm text-gray-500">
                    Menampilkan 50 dari {{ $log->transaksi->count() }} transaksi
                </div>
            @endif
        </div>
    @endif
</div>
@endsection