<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cocycle - Food Waste Donation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-green-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('donation.index') }}" class="flex items-center gap-2 font-bold text-xl hover:text-green-100 transition">
                <i class="fas fa-recycle"></i> Cocycle Donation
            </a>

            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('donation.index') }}" class="hover:text-green-200 transition {{ request()->routeIs('donation.index') ? 'font-bold underline' : '' }}">
                    Exploration
                </a>
                <a href="{{ route('donation.cart') }}" class="hover:text-green-200 transition flex items-center gap-1 {{ request()->routeIs('donation.cart') ? 'font-bold underline' : '' }}">
                    <i class="fas fa-shopping-basket"></i> Cart
                    @if(session('cart'))
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('donation.impact') }}" class="hover:text-green-200 transition {{ request()->routeIs('donation.impact') ? 'font-bold underline' : '' }}">
                    <i class="fas fa-chart-line"></i> Impact
                </a>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-green-800 flex items-center justify-center text-sm font-bold">
                    U
                </div>
                <span class="hidden md:inline text-sm">Hi, User</span>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><i class="fas fa-info-circle"></i> {{ session('info') }}</span>
            </div>
        </div>
    @endif

    <main class="container mx-auto px-4 py-8 min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} Cocycle - Food Waste Management.</p>
        </div>
    </footer>

</body>
</html>