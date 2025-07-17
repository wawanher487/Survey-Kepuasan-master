<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKuesionerRequest;
use App\Http\Requests\UpdateKuesionerRequest;
use App\Models\Kuesioner;
use App\Models\Unsur;
use App\Models\Village;
use Illuminate\Http\Request;

class KuesionerController extends Controller
{


    public function index(Request $request)
    {
        $villages = Village::all();

        // Query awal
        $query = Kuesioner::with(['unsur', 'village'])->latest();

        // Filter jika ada village_id di query string
        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        $kuesioner = $query->paginate(5)->withQueryString(); // withQueryString supaya filter tidak hilang saat paginasi

        return view('pages.dashboard.kuesioner.index', compact('kuesioner', 'villages'));
    }
    public function create()
    {
        if (!session('sudah_verifikasi')) {
            return redirect()->route('verifikasi.form')->with('error', 'Silakan verifikasi email terlebih dahulu.');
        }

        $unsurs = Unsur::all();
        $villages = Village::all();
        return view('pages.dashboard.kuesioner.create', compact('unsurs', 'villages'));
    }

    public function store(StoreKuesionerRequest $request)
    {

        try {
            Kuesioner::create($request->only('question', 'unsur_id', 'village_id'));

            // Hapus session verifikasi agar tidak bisa isi lagi
            $request->session()->forget(['sudah_verifikasi', 'token_verifikasi', 'token_expired_at', 'email_verifikasi']);

            return redirect()
                ->route('kuesioner.index')
                ->with('success', 'Data berhasil disimpan!');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['message' => ['Terjadi kesalahan saat menyimpan data!', $th->getMessage()]]);
        }
    }

    public function edit(Kuesioner $kuesioner)
    {
        try {
            $unsurs = Unsur::all();
            $villages = Village::all();

            return view('pages.dashboard.kuesioner.edit', compact('kuesioner', 'unsurs', 'villages'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['message' => ['Terjadi kesalahan saat mengambil data!', $th->getMessage()]]);
        }
    }

    public function update(UpdateKuesionerRequest $request, Kuesioner $kuesioner)
    {
        try {
            $kuesioner->question = $request->question;
            $kuesioner->unsur_id = $request->unsur_id;
            $kuesioner->village_id = $request->village_id;
            $kuesioner->update();
            return redirect()->route('kuesioner.index', $kuesioner->uuid)->with('success', 'Data berhasil diedit!');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['message' => ['Terjadi kesalahan saat mengedit data!', $th->getMessage()]]);
        }
    }

    public function destroy(Kuesioner $kuesioner)
    {
        try {
            Kuesioner::destroy($kuesioner->id);
            return redirect()->route('kuesioner.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['message' => ['Terjadi kesalahan saat menghapus data!', $th->getMessage()]]);
        }
    }

    public function checks(Request $request)
    {
        try {
            $action = $request->action;
            $checks = $request->checks;

            if ($action == 'delete') {
                Kuesioner::whereIn('uuid', $checks)->delete();
            }

            return redirect()
                ->back()
                ->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors(['message' => ['Terjadi kesalahan saat menghapus data', $th->getMessage()]]);
        }
    }
}