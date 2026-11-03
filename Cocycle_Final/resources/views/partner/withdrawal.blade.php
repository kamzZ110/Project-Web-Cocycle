<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarik Dana - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Sesuai Palette */
        :root { 
            --green-sage: #8A9A5B; 
            --cream: #FFF7E0;
            --pale-yellow: #EEE9B0;
            --bg-site: #fafafaff;
            --dark-text: #333;
        }
        body { background-color: var(--bg-site); font-family: 'Poppins', sans-serif; color: var(--dark-text); }
        
        /* Header Section Clean */
        .header-section { background: white; padding: 15px 0; border-bottom: 1px solid #eee; }
        .page-title-area { padding: 30px 0; }

        /* Saldo Card */
        .saldo-card {
            background-color: var(--pale-yellow); /* Warna Palette */
            border-radius: 20px;
            padding: 30px;
            border: none;
            height: 100%;
        }
        .saldo-label { font-size: 0.9rem; font-weight: 600; color: #6b6b6b; text-transform: uppercase; letter-spacing: 1px; }
        .saldo-amount { font-size: 2.5rem; font-weight: 800; color: var(--green-sage); margin: 10px 0 30px 0; }
        
        .saldo-action {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--green-sage);
            font-weight: 700;
            background: white;
            padding: 12px;
            border-radius: 12px;
            border: 2px solid var(--green-sage);
            transition: 0.3s;
        }
        .saldo-action:hover { background-color: var(--green-sage); color: white; }
        .saldo-action i { font-size: 1.2rem; margin-right: 8px; }

        /* Transaction List */
        .transaction-section { padding-left: 20px; }
        .transaction-section h4 { color: var(--green-sage); font-weight: 800; margin-bottom: 25px; }
        .date-group { font-weight: 700; font-size: 0.9rem; margin-top: 25px; margin-bottom: 15px; color: #888; }
        
        .trans-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .trans-icon {
            width: 45px; height: 45px;
            background-color: var(--green-sage);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; margin-right: 15px;
            position: relative;
        }
        .status-arrow {
            position: absolute; bottom: -2px; right: -2px;
            width: 20px; height: 20px;
            background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; border: 2px solid white;
        }
        .arrow-up { color: var(--green-sage); background: #e8f5e9; }
        .arrow-down { color: #dc3545; background: #f8d7da; }

        .trans-desc { flex-grow: 1; font-size: 1rem; font-weight: 600; }
        .trans-value { font-weight: 700; font-size: 1rem; }
        .text-income { color: var(--green-sage); }
        .text-outcome { color: var(--dark-text); }

        /* Back Button */
        .btn-back { color: var(--dark-text); text-decoration: none; font-size: 1.3rem; transition: 0.3s; }
        .btn-back:hover { color: var(--green-sage); }
    </style>
</head>
<body>

    <div class="header-section">
        <div class="container-fluid px-5 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('partner.home') }}" class="btn-back me-3"><i class="bi bi-arrow-left"></i></a>
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-shop me-2"></i>{{ strtoupper($nama_toko) }}</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-2 fw-bold small">{{ $user->username }}</span>
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 mb-5">
        <div class="page-title-area">
            <h2 class="fw-bold">Tarik Dana & Analisis</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="row g-5">
            <div class="col-lg-5">
                <div class="saldo-card shadow-sm">
                    <div class="saldo-label">Saldo Tersedia</div>
                    <div class="saldo-amount">Rp {{ number_format((float) $user->balance, 0, ',', '.') }}</div>
                    <p class="small text-muted">Pendapatan berasal dari pesanan selesai setelah potongan aplikasi 10%. Pengajuan akan menahan saldo sampai dibatalkan atau diproses manual.</p>

                    <form method="POST" action="{{ route('partner.withdrawal.store') }}" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Nominal penarikan</label>
                            <input id="amount" name="amount" type="number" min="1" step="0.01" max="{{ (float) $user->balance }}" value="{{ old('amount') }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Bank / e-wallet tujuan</label>
                            <input id="bank_name" name="bank_name" value="{{ old('bank_name') }}" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_name" class="form-label fw-semibold">Nama pemilik rekening</label>
                            <input id="account_name" name="account_name" value="{{ old('account_name') }}" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_number" class="form-label fw-semibold">Nomor rekening / akun</label>
                            <input id="account_number" name="account_number" value="{{ old('account_number') }}" class="form-control" maxlength="50" required>
                        </div>
                        <button type="submit" class="saldo-action w-100"><i class="bi bi-cash-stack"></i> Ajukan Penarikan</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7 transaction-section" id="history">
                <h4>Riwayat Transaksi</h4>

                <p class="small text-muted">Pengajuan di halaman ini dicatat sebagai permintaan. Transfer bank belum otomatis karena proyek belum terhubung ke payment gateway atau panel admin pencairan.</p>
                @forelse ($transactions as $transaction)
                    <div class="trans-item">
                        <div class="trans-icon" style="{{ $transaction->type === 'withdrawal' ? 'background:#495057' : '' }}">
                            <i class="bi {{ $transaction->type === 'income' ? 'bi-bag-fill' : 'bi-wallet2' }}"></i>
                        </div>
                        <div class="trans-desc">
                            <div>{{ $transaction->type === 'income' ? 'Pendapatan pesanan' : 'Pengajuan penarikan' }}{{ $transaction->order?->invoice_number ? ' #'.$transaction->order->invoice_number : '' }}</div>
                            <small class="text-muted">{{ $transaction->created_at?->format('d M Y H:i') }} · {{ $transaction->status === 'confirmed' ? 'Berhasil' : ($transaction->status === 'pending' ? 'Menunggu diproses' : 'Dibatalkan') }}
                                @if ($transaction->type === 'withdrawal') · {{ $transaction->bank_name }} · {{ $transaction->account_number }} @endif
                            </small>
                        </div>
                        <div class="trans-value {{ $transaction->type === 'income' ? 'text-income' : 'text-outcome' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                            @if ($transaction->type === 'withdrawal' && $transaction->status === 'pending')
                                <form method="POST" action="{{ route('partner.withdrawal.cancel', $transaction->id) }}" class="mt-2">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Batalkan</button></form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Belum ada transaksi saldo.</p>
                @endforelse
                <a href="{{ route('partner.orders.index') }}" class="saldo-action mt-4">Lihat Riwayat Pesanan</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
</body>
</html>
