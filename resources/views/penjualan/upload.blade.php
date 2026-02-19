<!-- resources/views/penjualan/upload.blade.php -->
@extends('layouts.app')

@section('title', 'Upload Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Upload Data Penjualan</h2>
            <p class="mt-1 text-sm text-gray-600">
                Upload menggunakan template atau langsung dari CSV marketplace
            </p>
        </div>
        
        <!-- Download Template Button -->
        <a href="{{ route('penjualan.template') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download Template Excel
        </a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Upload File</h3>
        </div>
        
        <form action="{{ route('penjualan.upload.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <!-- Upload Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Tipe Upload <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label id="label-template"
                          class="flex items-start p-4 border-2 border-indigo-500 bg-indigo-50 rounded-lg cursor-pointer transition">
                        <input type="radio" name="upload_type" value="template" class="mt-1" checked>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">Template SIAPRIZ (Recommended)</div>
                            <div class="text-sm text-gray-600">Upload menggunakan template Excel yang sudah disediakan. Format konsisten untuk semua marketplace.</div>
                        </div>
                    </label>
                    
                    <label id="label-direct"
                        class="flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer transition">
                        <input type="radio" name="upload_type" value="direct" class="mt-1">
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">CSV Langsung dari Marketplace</div>
                            <div class="text-sm text-gray-600">Upload file CSV/Excel langsung dari Shopee, Tokopedia, atau Lazada (format asli marketplace).</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Marketplace Selection -->
            <div>
                <label for="id_marketplace" class="block text-sm font-medium text-gray-700 mb-2">
                    Marketplace <span class="text-red-500">*</span>
                </label>
                <select name="id_marketplace" id="id_marketplace" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Marketplace --</option>
                    @foreach($marketplaces as $marketplace)
                        <option value="{{ $marketplace->id_marketplace }}">{{ $marketplace->nama_marketplace }}</option>
                    @endforeach
                </select>
                @error('id_marketplace')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    File Excel/CSV <span class="text-red-500">*</span>
                </label>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                <span>Upload file</span>
                                <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">XLSX, XLS, CSV hingga 10MB</p>
                    </div>
                </div>
                
                <div id="file-name" class="mt-2 text-sm text-gray-600 hidden"></div>
                
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div id="info-template" class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3 text-sm text-blue-700">
                        <p class="font-semibold mb-2">Cara Upload dengan Template:</p>
                        <ol class="list-decimal ml-5 space-y-1">
                            <li>Klik tombol "Download Template Excel"</li>
                            <li>Buka file template, isi data sesuai instruksi</li>
                            <li>Pilih marketplace yang sesuai</li>
                            <li>Upload file yang sudah diisi</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <div id="info-direct" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded hidden">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3 text-sm text-yellow-700">
                        <p class="font-semibold mb-2">Upload CSV Langsung:</p>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>File harus format asli dari marketplace (jangan diubah)</li>
                            <li>Pastikan pilih marketplace yang sesuai</li>
                            <li>Format harus sesuai dengan yang didukung sistem</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Upload & Proses
                </button>
            </div>
        </form>
    </div>

    <!-- Upload History -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Upload</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marketplace</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($history as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->tanggal_upload->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="truncate max-w-xs">{{ $log->nama_file }}</span>
                                    @if($log->hasFile())
                                        <a href="{{ route('penjualan.download', $log->id_upload) }}" 
                                           class="ml-2 text-indigo-600 hover:text-indigo-900" 
                                           title="Download file">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->marketplace->nama_marketplace }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status_upload === 'selesai')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Selesai
                                    </span>
                                @elseif($log->status_upload === 'proses')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Proses
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>Sukses: {{ $log->baris_sukses }}</div>
                                @if($log->baris_gagal > 0)
                                    <div class="text-red-600">Gagal: {{ $log->baris_gagal }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <a href="{{ route('penjualan.upload-detail', $log->id_upload) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Detail
                                </a>
                                
                                @if($log->canBeDeleted())
                                    <button 
                                        onclick="confirmDelete({{ $log->id_upload }}, '{{ $log->nama_file }}', {{ $log->getTransactionCount() }})"
                                        class="text-red-600 hover:text-red-900">
                                        Hapus
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada riwayat upload
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
    // ===== Upload type UI toggle =====
    const uploadTypeRadios = document.querySelectorAll('input[name="upload_type"]');
    const labelTemplate = document.getElementById('label-template');
    const labelDirect = document.getElementById('label-direct');
    const infoTemplate = document.getElementById('info-template');
    const infoDirect = document.getElementById('info-direct');

    function setActiveLabel(type) {
        // reset
        [labelTemplate, labelDirect].forEach(label => {
            label.classList.remove('border-indigo-500', 'bg-indigo-50');
            label.classList.add('border-gray-300', 'bg-white');
        });

        // active
        if (type === 'template') {
            labelTemplate.classList.add('border-indigo-500', 'bg-indigo-50');
            labelTemplate.classList.remove('border-gray-300');
        } else {
            labelDirect.classList.add('border-indigo-500', 'bg-indigo-50');
            labelDirect.classList.remove('border-gray-300');
        }
    }

    uploadTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            setActiveLabel(this.value);

            // reset file display
            const fileInput = document.getElementById('file');
            const fileNameEl = document.getElementById('file-name');
            fileInput.value = '';
            fileNameEl.classList.add('hidden');
            fileNameEl.textContent = '';

            // info box toggle
            if (this.value === 'template') {
                infoTemplate.classList.remove('hidden');
                infoDirect.classList.add('hidden');
            } else {
                infoTemplate.classList.add('hidden');
                infoDirect.classList.remove('hidden');
            }
        });
    });

    // ===== File input preview =====
    const fileInput = document.getElementById('file');
    const fileNameEl = document.getElementById('file-name');

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            fileNameEl.textContent = `File dipilih: ${this.files[0].name}`;
            fileNameEl.classList.remove('hidden');
            fileNameEl.classList.add('text-indigo-600', 'font-medium');
        }
    });

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