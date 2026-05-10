<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcarTrocaSenha
{
    private const ROTAS_LIBERADAS = ['senha.trocar', 'logout', 'senha.atualizar'];

    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->force_password_change &&
            ! in_array($request->route()?->getName(), self::ROTAS_LIBERADAS)
        ) {
            return redirect()->route('senha.trocar')
                ->with('aviso', 'Por segurança, você precisa definir uma nova senha antes de continuar.');
        }

        return $next($request);
    }
}
