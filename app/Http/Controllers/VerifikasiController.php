<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class VerifikasiController extends Controller
{
    public function index()
    {
        if (session('sudah_verifikasi')) {
            return redirect()->route('kuesioner');
        }
        return view('verifikasi');
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $token = strtoupper(Str::random(6));

        // Simpan ke session
        session([
            'email_verifikasi' => $request->email,
            'token_verifikasi' => $token,
            'token_expired_at' => now()->addMinutes(1),
        ]);

        // Kirim email
        \Mail::raw("Kode verifikasi Anda adalah: $token", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Kode Verifikasi Kuesioner');
        });

        return redirect()->route('verifikasi.form')->with('success', 'Kode verifikasi telah dikirim ke email Anda.');
    }


    public function proses(Request $request)
    {
        $request->validate([
            'kode_akses' => 'required',
        ]);

        $kodeInput = $request->input('kode_akses');
        $kodeBenar = session('token_verifikasi');
        $expiredAt = session('token_expired_at');

        if (!$kodeBenar || !$expiredAt) {
            return back()->with('error', 'Kode verifikasi tidak ditemukan. Silakan kirim ulang.');
        }

        if (now()->gt($expiredAt)) {
            return back()->with('error', 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang.');
        }

        // Cek apakah kodenya cocok
        if ($kodeInput !== $kodeBenar) {
            return back()->with('error', 'Kode yang Anda masukkan salah.');
        }

        // Jika cocok dan belum expired, tandai verifikasi berhasil
        $request->session()->put('sudah_verifikasi', true);
        // $request->session()->forget(['token_verifikasi', 'token_expired_at', 'email_verifikasi']);
        return redirect()->route('kuesioner');
    }


    public function showForm()
    {
        return view('verifikasi');
    }


}
