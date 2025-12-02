@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row gap-8">
    
    <div class="lg:w-2/3">
        <h2 class="text-2xl font-bold mb-4">Checkout Donasi</h2>
        
        <form action="{{ route('donation.process') }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">1. Pilih Komunitas Penerima</h3>
                <div class="flex gap-4 items-end">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan NGO</label>
                        <select name="community_id" id="communitySelect" required class="w-full border rounded p-2">
                            <option value="">-- Pilih Komunitas --</option>
                            @foreach($communities as $comm)
                                <option value="{{ $comm->id }}">{{ $comm->name }} ({{ $comm->focus_area }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="fetchCommunityInfo()" class="bg-blue-100 text-blue-600 px-4 py-2 rounded h-[42px]">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>

                <div id="communityInfoBox" class="hidden mt-4 p-4 bg-blue-50 rounded text-sm text-blue-800 border border-blue-200">
                    <p id="commDesc" class="mb-1"></p>
                    <p id="commAddress" class="text-xs text-gray-500"></p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">2. Metode Pembayaran</h3>
                <select name="payment_method" class="w-full border rounded p-2">
                    <option value="GOPAY">GoPay</option>
                    <option value="OVO">OVO</option>
                    <option value="BCA">Transfer BCA</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700">
                Bayar & Donasikan
            </button>
        </form>
    </div>

    <div class="lg:w-1/3">
        <div class="bg-white p-6 rounded-lg shadow sticky top-4">
            <h3 class="text-lg font-bold mb-4">Ringkasan</h3>
            @php $total = 0; @endphp
            <ul class="divide-y divide-gray-200 mb-4">
                @if(session('cart'))
                    @foreach(session('cart') as $id => $details)
                    @php $subtotal = $details['price'] * $details['quantity']; $total += $subtotal; @endphp
                    <li class="py-3 flex justify-between text-sm">
                        <div>
                            <span class="font-medium block">{{ $details['name'] }}</span>
                            <span class="text-gray-500 text-xs">{{ $details['quantity'] }} {{ $details['unit'] }} x {{ $details['price'] }}</span>
                        </div>
                        <span class="font-medium">Rp {{ number_format($subtotal) }}</span>
                    </li>
                    @endforeach
                @endif
            </ul>
            
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between text-sm"><span>Subtotal</span><span>Rp {{ number_format($total) }}</span></div>
                <div class="flex justify-between text-sm"><span>Ongkir</span><span>Rp 10.000</span></div>
                <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                    <span>Total</span>
                    <span class="text-green-600">Rp {{ number_format($total + 10000) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fetchCommunityInfo() {
        const id = document.getElementById('communitySelect').value;
        if(!id) return alert('Pilih komunitas dulu');

        // Fetch ke route Laravel yang kita buat tadi
        fetch(`/donation/community/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('commDesc').innerText = data.description;
                document.getElementById('commAddress').innerText = data.address;
                document.getElementById('communityInfoBox').classList.remove('hidden');
            });
    }
</script>
@endsection