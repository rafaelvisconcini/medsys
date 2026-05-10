<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaBloqueio;
use App\Models\Profissional;
use Illuminate\Http\Request;

class BloqueioController extends Controller
{
    public function index()
    {
        $profissionais = Profissional::with('user')->where('ativo', true)->get();
        $bloqueios = AgendaBloqueio::with('profissional.user')
            ->orderByDesc('data_inicio')
            ->paginate(20);

        return view('admin.bloqueios.index', compact('profissionais', 'bloqueios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'profissional_id' => 'required|exists:profissionais,id',
            'data_inicio'     => 'required|date',
            'data_fim'        => 'required|date|after_or_equal:data_inicio',
            'motivo'          => 'nullable|string|max:255',
        ], [
            'data_fim.after_or_equal' => 'A data fim deve ser igual ou posterior à data início.',
        ]);

        AgendaBloqueio::create($request->only('profissional_id', 'data_inicio', 'data_fim', 'motivo'));

        return back()->with('success', 'Bloqueio registrado.');
    }

    public function destroy(AgendaBloqueio $bloqueio)
    {
        $bloqueio->delete();
        return back()->with('success', 'Bloqueio removido.');
    }
}
