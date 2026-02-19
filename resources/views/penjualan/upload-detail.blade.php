<!-- resources/views/penjualan/upload-detail.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Upload')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('penjualan.upload') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                ← Kembali ke Upload
            </a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Detail Upload</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $log->nama_file }}</p>
        </div>
        
        @if($log->canBeDeleted())
            <button 
                onclick="confirmDelete({{ $log->id_upload }}, '{{ $log->nama_file }}', {{ $log->getTransactionCount() }})"
                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus Upload
            </button>
        @endif
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
                    <dd class="mt-1 text-sm text-gray-900">{{ $log->getFileSizeFormatted() }}</dd>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Icon Warning -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <div class="mt-2 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Upload?</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Anda akan menghapus upload:
                    </p>
                    <p class="text-sm font-semibold text-gray-900 mt-2" id="deleteFileName"></p>
                    <p class="text-sm text-red-600 font-medium mt-3" id="deleteImpact"></p>
                    <p class="text-xs text-gray-500 mt-2">
                        Data tidak akan benar-benar terhapus, hanya dinonaktifkan dari sistem.
                    </p>
                </div>
            </div>
            
            <div class="items-center px-4 py-3 flex gap-3">
                <button 
                    onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== Delete Modal =====
    function confirmDelete(uploadId, fileName, transactionCount) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const fileNameEl = document.getElementById('deleteFileName');
        const impactEl = document.getElementById('deleteImpact');
        
        // Set form action
        form.action = `/penjualan/upload/${uploadId}`;
        
        // Set file name
        fileNameEl.textContent = fileName;
        
        // Set impact message
        impactEl.textContent = `${transactionCount} transaksi akan dinonaktifkan dari dashboard`;
        
        // Show modal
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>

@endsection