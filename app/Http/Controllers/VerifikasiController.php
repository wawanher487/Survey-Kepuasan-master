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

        // Generate token 6 karakter angka (agar mudah diketik)
        $token = random_int(100000, 999999);

        // Simpan ke session
        session([
            'email_verifikasi' => $request->email,
            'token_verifikasi' => $token,
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

        if ($kodeInput == $kodeBenar) {
            // Tandai berhasil
            session()->put('sudah_verifikasi', true);
            return redirect()->route('kuesioner');
        }

        return redirect()->back()->with('error', 'Kode yang Anda masukkan salah.');
    }


    public function showForm()
    {
        return view('verifikasi');
    }


}
