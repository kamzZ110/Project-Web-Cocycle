<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Toko - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { 
            --green-sage: #6B8E23; 
            --green-light: #9ACD32;
            --teal: #20B2AA;
            --orange: #FF8C42;
            --gold: #F6C55A; 
            --pale: #EEE9B0; 
            --bg: #fafafaff; 
        }
        body { background-color: var(--bg); font-family: 'Poppins', sans-serif; }
        
        /* Header & Tabs - Vibrant Gradient */
        .header-bg { 
            background: linear-gradient(135deg, #6B8E23 0%, #9ACD32 50%, #20B2AA 100%);
            border-bottom: none;
            padding-top: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .header-bg h5, .header-bg .nav-link, .header-bg span, .header-bg a { color: white !important; }
        
        .nav-tabs-custom { border-bottom: none; margin-top: 10px; }
        .nav-tabs-custom .nav-link { 
            color: rgba(255,255,255,0.7) !important;
            border: none; 
            padding: 15px 30px; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 0.5px; 
            transition: all 0.3s;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs-custom .nav-link:hover { 
            color: white !important;
            background: rgba(255,255,255,0.15);
        }
        .nav-tabs-custom .nav-link.active { 
            color: white !important;
            background: rgba(255,255,255,0.25);
            border-bottom: 3px solid white;
            font-weight: 700;
        }

        /* KPI Cards */
        .kpi-card {
            background: white; border-radius: 15px; padding: 20px;
            border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative; overflow: hidden; height: 100%;
        }
        .kpi-card::before {
            content: ""; position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 5px; border-radius: 0 5px 5px 0;
        }
        .border-green::before { background: #4CAF50; }
        .border-red::before { background: #FF5252; }
        
        .kpi-title { font-size: 0.85rem; color: #777; font-weight: 500; }
        .kpi-value { font-size: 1.25rem; font-weight: 700; margin: 10px 0; }
        .kpi-date { font-size: 0.7rem; color: #bbb; }

        /* Chart Box */
        .chart-card {
            background: white; border-radius: 15px; padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
</head>
<body>

    <div class="header-bg sticky-top shadow-sm">
        <div class="container-fluid px-5 d-flex justify-content-between align-items-center mb-1">
            <div class="d-flex align-items-center">
                <a href="{{ route('partner.home') }}" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h5 class="fw-bold mb-0 text-white"><i class="bi bi-shop me-2"></i>{{ strtoupper($store->store_name) }}</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-2 fw-bold small text-white">{{ $user->fullname ?? $user->username }}</span>
                <i class="bi bi-person-circle fs-3 text-white"></i>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs nav-tabs-custom">
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.products') }}">PRODUK</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.orders.index') }}">RIWAYAT PESANAN</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('partner.analysis.index') }}">ANALISIS</a></li>
            </ul>
        </div>
    </div>

    <div class="container my-5">
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="kpi-card border-green">
                    <div class="kpi-title">Penjualan Kotor</div>
                    <div class="kpi-value text-dark">Rp {{ number_format($total_gross, 2, ',', '.') }}</div>
                    <div class="kpi-date">Total Semua Waktu</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card border-green">
                    <div class="kpi-title">Penjualan Bersih</div>
                    <div class="kpi-value text-dark">Rp {{ number_format($net_income, 2, ',', '.') }}</div>
                    <div class="kpi-date">Setelah Komisi 5%</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card border-green">
                    <div class="kpi-title">Total Pesanan Selesai</div>
                    <div class="kpi-value text-dark">{{ $total_sold }}</div>
                    <div class="kpi-date">Total Transaksi</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card border-red">
                    <div class="kpi-title">Estimasi Komisi</div>
                    <div class="kpi-value text-dark">Rp {{ number_format($commission, 2, ',', '.') }}</div>
                    <div class="kpi-date">Biaya Aplikasi</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="fw-bold mb-4">Produk terjual</h6>
                    <canvas id="produkTerjualChart"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6 class="fw-bold mb-4">Penjualan Bersih</h6>
                    <canvas id="penjualanBersihChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data Dummy untuk Grafik sesuai legacy
        const labels = ['20 Okt', '30 Okt', '10 Nov', '20 Nov'];

        // Config Grafik Produk Terjual
        const ctx1 = document.getElementById('produkTerjualChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total',
                    data: [10, 15, 13, 20],
                    borderColor: '#9D6638',
                    backgroundColor: 'rgba(176, 186, 153, 0.24)',
                    borderWidth: 3,
                    tension: 0,
                    fill: true,
                    pointBackgroundColor: '#4E220F'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        // Config Grafik Penjualan Bersih
        const ctx2 = document.getElementById('penjualanBersihChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: [10, 100, 130, 200],
                    borderColor: '#9D6638',
                    borderWidth: 3,
                    tension: 0,
                    fill: false,
                    pointBackgroundColor: '#4E220F'
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>
