@extends('layouts.public')

@section('title', 'Verifikasi Akses')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <form action="{{ route('verifikasi.proses') }}" method="POST"
          class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        {{-- Pesan sukses --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error kode akses --}}
        @error('kode_akses')
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                {{ $message }}
            </div>
        @enderror

        <label class="block text-gray-700 text-sm font-bold mb-2">
            Masukkan Kode Akses
        </label>
        <input name="kode_akses" type="text" required
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               placeholder="Masukkan kode dari petugas">

        <div class="mt-4">
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Verifikasi
            </button>
        </div>
    </form>
</div>
@endsection
