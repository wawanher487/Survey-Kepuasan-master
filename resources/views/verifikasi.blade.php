@extends('layouts.public')

@section('title', 'Verifikasi Akses')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Masukkan Email Anda</h2>

        {{-- Notifikasi sukses --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notifikasi gagal --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Error validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form Email --}}
        <form method="POST" action="{{ route('verifikasi.kirim') }}">
            @csrf
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input name="email" type="email" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 focus:outline-none focus:shadow-outline"
                   placeholder="contoh@email.com">
            <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Kirim Kode Verifikasi
            </button>
        </form>

        {{-- Form Verifikasi Kode --}}
        @if(session('email_verifikasi'))
        <hr class="my-6">
        <form method="POST" action="{{ route('verifikasi.proses') }}">
            @csrf
            <label for="kode_akses" class="block text-gray-700 text-sm font-bold mb-2">Kode Verifikasi:</label>
            <input type="text" name="kode_akses" placeholder="Masukkan kode yang dikirim ke email"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 focus:outline-none focus:shadow-outline"
                   required>
            <button type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Verifikasi
            </button>
        </form>
        @endif
    </div>
</div>
@endsection
