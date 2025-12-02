@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Dampak Lingkungan Anda</h2>
    <p class="text-gray-600">Data ini diambil berdasarkan hasil olahan komunitas dari donasi Anda.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500">
        <div class="text-gray-500 text-sm">Total Donasi</div>
        <div class="text-3xl font-bold text-gray-800">{{ $stats['total_donation'] }} <span class="text-sm font-normal text-gray-400">kali</span></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
        <div class="text-gray-500 text-sm">Sampah Terselamatkan</div>
        <div class="text-3xl font-bold text-gray-800">{{ $stats['total_waste_saved'] }} <span class="text-sm font-normal text-gray-400">kg</span></div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-purple-500">
        <div class="text-gray-500 text-sm">CO2 Dicegah</div>
        <div class="text-3xl font-bold text-gray-800">{{ $stats['total_co2_saved'] }} <span class="text-sm font-normal text-gray-400">kg</span></div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h3 class="font-bold text-xl mb-4">Laporan Hasil Olahan</h3>
    
    @if($impacts->count() > 0)
        <div class="space-y-6">
            @foreach($impacts as $impact)
            <div class="flex flex-col md:flex-row gap-4 border-b pb-4 last:border-0">
                <div class="bg-green-50 p-3 rounded-lg text-center min-w-[80px]">
                    <i class="fas fa-leaf text-2xl text-green-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between mb-1">
                        <h4 class="font-bold text-lg">{{ $impact->product_produced }}</h4>
                        <span class="text-sm text-gray-500">{{ $impact->created_at->format('d M Y') }}</span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-2">
                        <span class="font-semibold">Hasil:</span> {{ $impact->product_qty_result }} 
                        dari donasi ke <b>{{ $impact->donation->community->name }}</b>
                    </p>

                    @if($impact->community_note)
                    <div class="bg-yellow-50 p-3 rounded text-xs text-yellow-800 italic">
                        "{{ $impact->community_note }}"
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            
            <p>Belum ada data dampak. Tunggu komunitas mengolah donasimu ya!</p>
        </div>
    @endif
</div>
@endsection