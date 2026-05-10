<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarUsuarioAtivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! auth()->user()->ativo) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Sua conta está desativada. Entre em contato com o administrador.']);
        }

        return $next($request);
    }
}
