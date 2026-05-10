<?php

namespace App\Http\Controllers\Lgpd;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(50);

        return view('lgpd.audit.index', compact('logs'));
    }

    public function paciente(Paciente $paciente)
    {
        $logs = Activity::with('causer')
            ->where('subject_type', Paciente::class)
            ->where('subject_id', $paciente->id)
            ->latest()
            ->paginate(30);

        return view('lgpd.audit.paciente', compact('paciente', 'logs'));
    }
}
