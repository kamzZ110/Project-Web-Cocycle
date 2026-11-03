<?php

namespace App\Http\Controllers;

use App\Models\PartnerWalletTransaction;
use App\Models\User;
use App\Services\PartnerWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerWithdrawalController extends Controller
{
    public function index(PartnerWallet $wallet)
    {
        $user = $wallet->reconcile(Auth::user());
        $transactions = PartnerWalletTransaction::where('user_id', $user->id)
            ->latest()->get();
        $nama_toko = $user->store?->store_name ?? $user->nama_toko ?? 'Toko Anda';

        return view('partner.withdrawal', compact('user', 'transactions', 'nama_toko'));
    }

    public function store(Request $request, PartnerWallet $wallet)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'decimal:0,2'],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
        ]);

        $user = $wallet->reconcile(Auth::user());

        DB::transaction(function () use ($data, $user) {
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $amount = round((float) $data['amount'], 2);

            if ($amount > (float) $lockedUser->balance) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Nominal penarikan melebihi saldo yang tersedia.',
                ]);
            }

            PartnerWalletTransaction::create([
                'user_id' => $lockedUser->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'status' => 'pending',
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
            ]);

            // Reserve the funds while the request awaits manual processing.
            $lockedUser->balance = (float) $lockedUser->balance - $amount;
            $lockedUser->save();
        });

        return redirect()->route('partner.withdrawal')->with('success', 'Pengajuan penarikan tercatat dan menunggu diproses. Belum ada transfer otomatis.');
    }

    public function cancel($id)
    {
        DB::transaction(function () use ($id) {
            $user = User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $withdrawal = PartnerWalletTransaction::where('user_id', $user->id)
                ->where('type', 'withdrawal')->where('status', 'pending')
                ->lockForUpdate()->findOrFail($id);

            $withdrawal->status = 'cancelled';
            $withdrawal->save();
            $user->balance = (float) $user->balance + (float) $withdrawal->amount;
            $user->save();
        });

        return redirect()->route('partner.withdrawal')->with('success', 'Pengajuan dibatalkan dan saldo dikembalikan.');
    }
}
