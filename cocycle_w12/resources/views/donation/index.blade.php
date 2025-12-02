@extends('layouts.app')

@section('content')
<div class="mb-8 text-center">
    <h2 class="text-3xl font-bold text-green-800">Selamatkan Makanan, Bantu Komunitas</h2>
    <p class="text-gray-600">Marketplace sisa layak olah. Stok dinamis dari database.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @forelse($items as $item)
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/300x200?text=Food+Waste' }}" 
             alt="{{ $item->name }}" class="w-full h-48 object-cover">
        
        <div class="p-5">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-bold text-lg">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500"><i class="fas fa-store mr-1"></i> {{ $item->store_name }}</p>
                </div>
                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">
                    Sisa: {{ $item->quantity_available }} {{ $item->unit }}
                </span>
            </div>

            <div class="my-3">
                <p class="text-xs text-gray-400 mb-1">Potensi Olahan:</p>
                <div class="flex flex-wrap gap-2">
                    @if($item->tags)
                        @foreach($item->tags as $tag)
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full border border-green-200">
                            {{ $tag }}
                        </span>
                        @endforeach
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-center mt-4 border-t pt-3">
                <div>
                    <span class="block text-xs text-gray-500">Biaya Handling</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($item->handling_fee, 0, ',', '.') }}</span>
                </div>
                <form action="{{ route('donation.add_cart', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm transition">
                        <i class="fas fa-plus"></i> Ambil
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-10">
        <p class="text-gray-500">Belum ada makanan sisa yang tersedia saat ini.</p>
    </div>
    @endforelse
</div>
@endsection