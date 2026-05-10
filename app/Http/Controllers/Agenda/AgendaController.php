<?php

namespace App\Http\Controllers\Agenda;

use App\Http\Controllers\Controller;
use App\Models\Profissional;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function __construct(private AgendaService $agenda) {}

    public function index()
    {
        $profissionais = Profissional::with('user')->where('ativo', true)->get();
        return view('agenda.index', compact('profissionais'));
    }

    public function slots(Request $request)
    {
        $request->validate([
            'start'           => 'required|date',
            'end'             => 'required|date',
            'profissional_id' => 'nullable|integer|exists:profissionais,id',
        ]);

        $inicio = Carbon::parse($request->start);
        $fim    = Carbon::parse($request->end);

        return response()->json(
            $this->agenda->eventosParaCalendario($inicio, $fim, $request->profissional_id)
        );
    }
}
