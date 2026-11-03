@extends('layouts.donation')

@push('styles')
<style>
    .ls-1 { letter-spacing: 1px; }
</style>
@endpush

@section('content')
<div class="container mt-4 mb-5">
    
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center mb-5 shadow-sm border-0" role="alert" style="background-color: #f0fdf4; border-left: 5px solid #5CB338 !important;">
        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
        <div>
            <h4 class="alert-heading fw-bold mb-0">Donasi Berhasil!</h4>
            <p class="mb-0 text-muted">Terima kasih! Donasimu telah tercatat untuk komunitas.</p>
        </div>
    </div>
    @endif

    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Pelaporan Dampak</h2>
            <p class="text-muted">Pantau kontribusi kebaikanmu di sini.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('donation.levels') }}" class="text-decoration-none">
                <div class="card d-inline-block px-4 py-2 {{ $level['border'] }}" style="border-width: 2px;">
                    <div class="d-flex align-items-center">
                        <i class="fas {{ $level['icon'] }} fa-2x {{ $level['color'] }} me-3"></i>
                        <div class="text-start text-dark">
                            <small class="text-muted d-block lh-1">Level Donasi</small>
                            <span class="fw-bold {{ $level['color'] }}">{{ $level['name'] }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 display-metrics">
        <!-- Metrics Section -->
        <div class="col-12 mb-4">
            <div class="row g-3 text-center">
                <div class="col-md-6">
                    <div class="card p-3 border-0 shadow-sm" style="background-color: #e0f2f1;">
                        <h6 class="text-success fw-bold text-uppercase ls-1">Jejak Karbon Dicegah</h6>
                        <div class="display-5 fw-bold text-dark mt-2">{{ number_format($total_co2, 1) }} <small class="fs-6 text-muted">kg CO2</small></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3 border-0 shadow-sm" style="background-color: #fff8e1;">
                        <h6 class="text-warning fw-bold text-uppercase ls-1">Limbah Makanan Diselamatkan</h6>
                        <div class="display-5 fw-bold text-dark mt-2">{{ number_format($total_waste, 1) }} <small class="fs-6 text-muted">kg</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-4 h-100 shadow-sm border-0">
                <h5 class="fw-bold mb-4">Grafik Perkembangan Donasi (Tahun {{ $current_year }})</h5>
                <canvas id="impactChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 h-100 text-center bg-white shadow-sm border-0">
                <h5 class="fw-bold mb-4">Poin Kamu</h5>
                <div class="display-4 fw-bold text-success mb-2">{{ number_format($my_points) }}</div>
                <p class="text-muted small">Poin Kebaikan</p>
                
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, ($my_points % 1000) / 10) }}%"></div>
                </div>
                
                <hr>
                <div class="text-start">
                    <h6 class="fw-bold mb-3"><i class="fas fa-gift me-2 text-danger"></i>Reward Tersedia</h6>
                    @if($my_points > 100)
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark border p-2">Voucher F&B 20%</span>
                        <span class="badge bg-light text-dark border p-2">E-Sertifikat</span>
                    </div>
                    @else
                        <p class="text-muted small">Kumpulkan 100 poin untuk melihat reward!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold mt-5 mb-3">Riwayat Donasi Terbaru</h4>
    <div class="row g-3">
        @forelse($history as $h)
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-success bg-opacity-10 text-success">{{ ucfirst($h->status) }}</span>
                            <small class="text-muted">{{ date('d M Y', strtotime($h->created_at)) }}</small>
                        </div>
                        <h6 class="fw-bold">{{ $h->community->name ?? 'Unknown Community' }}</h6>
                        <p class="mb-0 text-muted small">Total Donasi: Rp {{ number_format($h->total_amount) }}</p>
                        <small class="text-muted" style="font-size: 0.75rem;">Inv: {{ $h->invoice_number }}</small>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada riwayat donasi.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('impactChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Total Donasi (Rp)',
            data: {!! $js_chart_data !!},
            borderColor: '#A3B565',
            backgroundColor: 'rgba(163, 181, 101, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { borderDash: [5, 5] }, beginAtZero: true },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
