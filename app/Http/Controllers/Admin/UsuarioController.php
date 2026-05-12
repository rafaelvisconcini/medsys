<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PerfilUsuario;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::withTrashed()
            ->whereIn('perfil', [
                PerfilUsuario::Recepcionista->value,
                PerfilUsuario::Financeiro->value,
                PerfilUsuario::Admin->value,
                PerfilUsuario::Proprietario->value,
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $perfis = [PerfilUsuario::Recepcionista, PerfilUsuario::Financeiro, PerfilUsuario::Admin];
        return view('admin.usuarios.create', compact('perfis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email',
            'perfil' => 'required|in:recepcionista,financeiro,admin',
        ], [
            'email.unique' => 'Este e-mail já está em uso.',
        ]);

        User::create([
            'name'                  => $request->name,
            'email'                 => $request->email,
            'password'              => bcrypt('Trocar@Primeiro1'),
            'perfil'                => $request->perfil,
            'ativo'                 => true,
            'force_password_change' => true,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário criado. Senha temporária: Trocar@Primeiro1');
    }

    public function show(User $usuario)
    {
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $perfis = [PerfilUsuario::Recepcionista, PerfilUsuario::Financeiro, PerfilUsuario::Admin];
        return view('admin.usuarios.edit', compact('usuario', 'perfis'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $usuario->id,
            'perfil' => 'required|in:recepcionista,financeiro,admin',
            'ativo'  => 'nullable|boolean',
        ], [
            'email.unique' => 'Este e-mail já está em uso.',
        ]);

        $usuario->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'perfil' => $request->perfil,
            'ativo'  => $request->has('ativo'),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode desativar sua própria conta.');
        }

        if ($usuario->perfil === PerfilUsuario::Proprietario) {
            return back()->with('error', 'O proprietário do sistema não pode ser desativado.');
        }

        $usuario->update(['ativo' => false]);
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário desativado.');
    }
}
