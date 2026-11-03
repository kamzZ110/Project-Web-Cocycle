@extends('layouts.donation')

@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('donation.impact') }}" class="text-dark me-3"><i class="fas fa-arrow-left fa-lg"></i></a>
        <h3 class="fw-bold mb-0">Kelebihan Level Donasi</h3>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Level 1 -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="row align-items-center">
                    <div class="col-3 text-center">
                        <i class="fas fa-heart text-danger display-4 opacity-50"></i>
                        <h6 class="fw-bold mt-2 mb-0">Kebaikan Baru</h6>
                        <small class="text-muted">500 Points</small>
                    </div>
                    <div class="col-9">
                        <h5 class="fw-bold text-danger">Kebaikan Baru</h5>
                        <p class="small text-muted mb-2">Mulai perjalanan kebaikanmu dengan bonus di transaksi awal</p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-ticket-alt text-danger me-2"></i> Voucher Diskon Rp20.000 (tanpa minimum belanja)</li>
                            <li><i class="fas fa-percent text-secondary me-2"></i> Voucher Cashback 20% untuk setiap pembelian pertama</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 2 -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="row align-items-center">
                    <div class="col-3 text-center">
                        <i class="fas fa-heart text-danger display-3 opacity-75"></i>
                        <h6 class="fw-bold mt-2 mb-0">Pemberi Aktif</h6>
                        <small class="text-muted">3000 Points</small>
                    </div>
                    <div class="col-9">
                        <h5 class="fw-bold text-danger">Pemberi Aktif</h5>
                        <p class="small text-muted mb-2">Belanja lebih hemat sambil terus berdampak positif lewat donasi</p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-ticket-alt text-danger me-2"></i> 2x Voucher Diskon Rp25.000</li>
                            <li><i class="fas fa-percent text-secondary me-2"></i> 2x Voucher Cashback 50%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 3 -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="row align-items-center">
                    <div class="col-3 text-center">
                        <i class="fas fa-heart text-danger display-2"></i>
                        <h6 class="fw-bold mt-2 mb-0">Penolong Lingkungan</h6>
                        <small class="text-muted">5000 Points</small>
                    </div>
                    <div class="col-9">
                        <h5 class="fw-bold text-danger">Penolong Lingkungan</h5>
                        <p class="small text-muted mb-2">Apresiasi untuk kontribusimu menjaga lingkungan dan komunitas</p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-ticket-alt text-danger me-2"></i> 4x Voucher Diskon Rp45.000</li>
                            <li><i class="fas fa-percent text-secondary me-2"></i> 2x Voucher Cashback 15%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 4 -->
        <div class="col-md-8">
            <div class="card p-4 shadow-sm border-0 h-100 border-2 border-danger">
                <div class="row align-items-center">
                    <div class="col-3 text-center">
                        <i class="fas fa-heart text-danger display-1"></i>
                        <h6 class="fw-bold mt-2 mb-0">Pemimpin Gerakan</h6>
                        <small class="text-muted">7500 Points</small>
                    </div>
                    <div class="col-9">
                        <h5 class="fw-bold text-danger">Pemimpin Gerakan</h5>
                        <p class="small text-muted mb-2">Manfaat premium sebagai penghargaan tertinggi atas dedikasimu</p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-ticket-alt text-danger me-2"></i> 5x Voucher Diskon Rp75.000</li>
                            <li><i class="fas fa-percent text-secondary me-2"></i> 3x Voucher Cashback 20%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
