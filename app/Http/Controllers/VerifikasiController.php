<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function index()
    {
        return view('verifikasi'); // tampilkan form verifikasi
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'kode_akses' => 'required|string',
        ]);

        $kodeAksesValid = '1234'; // bisa disimpan di env atau config

        if ($request->kode_akses === $kodeAksesValid) {
            $request->session()->put('sudah_verifikasi', true);
            return redirect()->route('kuesioner')->with('success', 'Verifikasi berhasil.');
        }

        return back()->withErrors(['kode_akses' => 'Kode akses salah.'])->withInput();
    }
}
