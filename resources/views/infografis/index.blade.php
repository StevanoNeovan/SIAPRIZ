@extends('layouts.app')

@section('title', 'Infografis')

@section('content')
<div class="space-y-6">

    <!-- =====================
         FILTER PERIODE
    ====================== -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('infografis.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Mulai
                </label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai', $periode['mulai']) }}"
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Akhir
                </label>
                <input type="date"
                       name="tanggal_akhir"
                       value="{{ request('tanggal_akhir', $periode['akhir']) }}"
                       class="w-full px-3 py-2 border rounded-md">
            </div>

            <button class="px-6 py-2 bg-indigo-600 text-white rounded-md">
                Filter
            </button>
        </form>
    </div>

    <!-- =====================
         INFOGRAFIS MARKETPLACE
    ====================== -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Grafis Perbandingan Kinerja Penjualan Per Marketplace
        </h3>

        <div class="relative h-72">
            <canvas id="marketplaceChart"></canvas>
        </div>
    </div>

    <!-- =====================
         INFOGRAFIS PRODUK PER MARKETPLACE
    ====================== -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            Kinerja Penjualan Total Per Produk Per Marketplace
        </h3>

        <div class="space-y-6">
            @forelse($product_per_marketplace as $marketplaceName => $products)
                <div class="border border-gray-200 rounded-lg p-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="px-3 py-1 rounded-md text-white mr-2
                            {{ $marketplaceName === 'Shopee' ? 'bg-orange-500' : '' }}
                            {{ $marketplaceName === 'Tokopedia' ? 'bg-green-600' : '' }}
                            {{ $marketplaceName === 'Lazada' ? 'bg-blue-600' : '' }}
                            {{ $marketplaceName === 'Umum' ? 'bg-gray-600' : '' }}
                        ">
                            {{ $marketplaceName }}
                        </span>
                    </h4>

                    <div class="relative h-[420px]">
                        <canvas id="productChart_{{ \Illuminate\Support\Str::slug($marketplaceName) }}"></canvas>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center">
                    Tidak ada data produk per marketplace
                </p>
            @endforelse
        </div>
    </div>

</div>

<!-- =====================
     CHART.JS
====================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
/* =================================================
   MARKETPLACE BAR CHART
================================================= */
const marketplaceData = @json($marketplace_chart_data);

new Chart(document.getElementById('marketplaceChart'), {
    type: 'bar',
    data: {
        labels: marketplaceData.labels,
        datasets: [{
            label: 'Pendapatan',
            data: marketplaceData.pendapatan,
            backgroundColor: marketplaceData.colors,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx =>
                        'Pendapatan: Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                    afterLabel: ctx =>
                        'Total Order: ' + marketplaceData.total_order[ctx.dataIndex]
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + v.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>

<script>
/* =================================================
   PRODUCT INFOGRAFIS PER MARKETPLACE (VERTICAL)
================================================= */
@foreach($product_per_marketplace as $marketplaceName => $products)

    @php
        $labels = array_map(fn($p) => $p['nama_produk'] ?? 'Unknown', $products);
        $values = array_map(fn($p) => $p['total_pendapatan'] ?? 0, $products);
        $totalTerjual = array_map(fn($p) => $p['total_terjual'] ?? 0, $products);

        $colorMap = [
            'Shopee' => 'rgba(238, 77, 45, 0.75)',
            'Tokopedia' => 'rgba(3, 172, 14, 0.75)',
            'Lazada' => 'rgba(65, 105, 225, 0.75)',
            'Umum' => 'rgba(107, 114, 128, 0.75)',
        ];

        $barColor = $colorMap[$marketplaceName] ?? 'rgba(99, 102, 241, 0.75)';
    @endphp

    new Chart(
        document.getElementById('productChart_{{ \Illuminate\Support\Str::slug($marketplaceName) }}'),
        {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($values),
                    backgroundColor: '{{ $barColor }}',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx =>
                                'Pendapatan: Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                            afterLabel: ctx =>
                                'Total Terjual: ' + @json($totalTerjual)[ctx.dataIndex]
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp ' + v.toLocaleString('id-ID')
                        }
                    }
                }
            }
        }
    );
@endforeach
</script>
@endsection