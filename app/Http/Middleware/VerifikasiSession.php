<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifikasiSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            !$request->session()->get('sudah_verifikasi') &&
            !$request->is('verifikasi') &&
            !$request->is('verifikasi-kirim') &&
            !$request->is('verifikasi/proses')
        ) {
            return redirect('/verifikasi');
        }

        return $next($request);
    }
}
