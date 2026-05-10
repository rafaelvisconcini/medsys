<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SenhaController extends Controller
{
    public function form()
    {
        return view('auth.trocar-senha');
    }

    public function atualizar(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/'],
        ], [
            'password.regex' => 'A senha deve ter ao menos 1 letra maiúscula, 1 número e 1 caractere especial.',
        ]);

        auth()->user()->update([
            'password'              => Hash::make($request->password),
            'force_password_change' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Senha atualizada com sucesso.');
    }
}
