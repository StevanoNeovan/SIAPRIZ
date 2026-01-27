<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Filter Periode -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input 
                    type="date" 
                    id="tanggal_mulai" 
                    name="tanggal_mulai" 
                    value="{{ request('tanggal_mulai', $periode['mulai']) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input 
                    type="date" 
                    id="tanggal_akhir" 
                    name="tanggal_akhir" 
                    value="{{ request('tanggal_akhir', $periode['akhir']) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>
            <div>
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($summary['total_order']) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ $summary['total_pendapatan'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Item Terjual</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($summary['total_item']) }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Order</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ $summary['rata_rata_order'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Trend Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren Penjualan Harian</h3>
            <div class="relative h-64">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Marketplace Performance Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Performa Per Marketplace
            </h3>
            <div class="relative h-64">
                <canvas id="marketplaceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Requirement 1: Kinerja Penjualan Per Marketplace (Detail Table) -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Detail Kinerja Per Marketplace
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marketplace</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Terjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan Kotor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan Bersih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Margin (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($marketplace as $mp)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mp['nama'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($mp['total_order']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($mp['item_terjual']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($mp['pendapatan_kotor'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Rp {{ number_format($mp['komisi'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{ $mp['pendapatan_formatted'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mp['margin'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data marketplace</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Requirement 3: Top Products (Kinerja Total Per Produk) -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Produk Terlaris - Total Semua Marketplace
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ranking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Terjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($top_products as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($index < 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $index === 1 ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $index === 2 ? 'bg-orange-100 text-orange-800' : '' }}
                                        font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-900">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->nama_produk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->kategori ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_terjual) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">Rp {{ number_format($product->total_pendapatan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($product->jumlah_transaksi) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Requirement 2: Kinerja Produk Per Marketplace -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Kinerja Produk Per Marketplace
            </h3>
            <p class="text-sm text-gray-600 mt-1">Detail penjualan produk di setiap marketplace</p>
        </div>
        <div class="p-6">
            @forelse($product_per_marketplace as $marketplace_name => $products)
                <div class="mb-6 last:mb-0">
                    <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="bg-indigo-500 text-white px-3 py-1 rounded-md mr-2">{{ $marketplace_name }}</span>
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nama Produk</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total Terjual</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Pendapatan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Order</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $product['nama_produk'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($product['total_terjual']) }}</td>
                                        <td class="px-4 py-2 text-sm text-green-600 font-medium">{{ $product['total_pendapatan_formatted'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ number_format($product['jumlah_order']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p class="text-center text-sm text-gray-500 py-4">Belum ada data produk per marketplace</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    /* =========================
       SALES TREND CHART
    ==========================*/
    const salesData = @json($chart_data);

    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Pendapatan Harian',
                data: salesData.pendapatan,
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            'Pendapatan: Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                        afterLabel: (ctx) =>
                            'Total Order: ' + salesData.total_order[ctx.dataIndex]
                    }
                }
            }
        }
    });


    /* =========================
       MARKETPLACE CHART
    ==========================*/
    const marketplaceData = @json($marketplace_chart_data);
    

    new Chart(document.getElementById('marketplaceChart'), {
        type: 'bar',
        data: {
            labels: marketplaceData.labels,
            datasets: [{
                label: 'Pendapatan Bersih',
                data: marketplaceData.pendapatan,
                backgroundColor: marketplaceData.colors,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            'Pendapatan: Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                        afterLabel: (ctx) =>
                            'Total Order: ' + marketplaceData.total_order[ctx.dataIndex]
                    }
                },
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => 'Rp ' + v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
</script>
@endsection