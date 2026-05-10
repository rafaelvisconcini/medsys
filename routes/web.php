<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SenhaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'verify' => false]);

Route::middleware(['auth'])->group(function () {

    // Troca de senha obrigatória (disponível para qualquer autenticado)
    Route::get('senha/trocar', [SenhaController::class, 'form'])->name('senha.trocar');
    Route::post('senha/trocar', [SenhaController::class, 'atualizar'])->name('password.update');

    Route::middleware(['auth', \App\Http\Middleware\ForcarTrocaSenha::class])->group(function () {

        Route::get('/', DashboardController::class)->name('dashboard');

        // Pacientes — qualquer autenticado acessa (Policy controla o que cada um vê/edita)
        Route::resource('pacientes', \App\Http\Controllers\Pacientes\PacienteController::class);

        // Agenda e Sessões — apenas quem pode agendar
        Route::middleware('can:agendar')->group(function () {
            Route::get('agenda', [\App\Http\Controllers\Agenda\AgendaController::class, 'index'])->name('agenda.index');
            Route::get('agenda/slots', [\App\Http\Controllers\Agenda\AgendaController::class, 'slots'])->name('agenda.slots');
            Route::resource('sessoes', \App\Http\Controllers\Agenda\SessaoController::class)->except(['show']);
            Route::patch('sessoes/{sessao}/status', [\App\Http\Controllers\Agenda\SessaoController::class, 'atualizarStatus'])
                ->name('sessoes.status');
        });

        // Prontuários — admin e profissionais (Policy isola por especialidade)
        Route::middleware('can:ver-prontuario')->prefix('prontuarios')->name('prontuarios.')->group(function () {
            Route::get('{paciente}', [\App\Http\Controllers\Prontuario\ProntuarioController::class, 'show'])
                ->name('show');
            Route::resource('{prontuario}/evolucoes', \App\Http\Controllers\Prontuario\EvolucaoController::class)
                ->shallow();
            // Anexos de Prontuário
            Route::get('{prontuario}/anexos/create', [\App\Http\Controllers\Prontuario\ProntuarioAnexoController::class, 'create'])
                ->name('anexos.create');
            Route::post('{prontuario}/anexos', [\App\Http\Controllers\Prontuario\ProntuarioAnexoController::class, 'store'])
                ->name('anexos.store');
            Route::get('anexos/{anexo}/download', [\App\Http\Controllers\Prontuario\ProntuarioAnexoController::class, 'download'])
                ->name('anexos.download');
            Route::delete('anexos/{anexo}', [\App\Http\Controllers\Prontuario\ProntuarioAnexoController::class, 'destroy'])
                ->name('anexos.destroy');

            // Planos Terapêuticos
            Route::get('{prontuario}/planos/create', [\App\Http\Controllers\Prontuario\PlanoTerapeuticoController::class, 'create'])
                ->name('planos.create');
            Route::post('{prontuario}/planos', [\App\Http\Controllers\Prontuario\PlanoTerapeuticoController::class, 'store'])
                ->name('planos.store');
            Route::get('planos/{plano}/edit', [\App\Http\Controllers\Prontuario\PlanoTerapeuticoController::class, 'edit'])
                ->name('planos.edit');
            Route::put('planos/{plano}', [\App\Http\Controllers\Prontuario\PlanoTerapeuticoController::class, 'update'])
                ->name('planos.update');
            Route::delete('planos/{plano}', [\App\Http\Controllers\Prontuario\PlanoTerapeuticoController::class, 'destroy'])
                ->name('planos.destroy');

            // Encaminhamentos
            Route::get('{prontuario}/encaminhamentos/create', [\App\Http\Controllers\Prontuario\EncaminhamentoController::class, 'create'])
                ->name('encaminhamentos.create');
            Route::post('{prontuario}/encaminhamentos', [\App\Http\Controllers\Prontuario\EncaminhamentoController::class, 'store'])
                ->name('encaminhamentos.store');
            Route::patch('encaminhamentos/{encaminhamento}', [\App\Http\Controllers\Prontuario\EncaminhamentoController::class, 'update'])
                ->name('encaminhamentos.update');
            Route::delete('encaminhamentos/{encaminhamento}', [\App\Http\Controllers\Prontuario\EncaminhamentoController::class, 'destroy'])
                ->name('encaminhamentos.destroy');
            Route::get('encaminhamentos/{encaminhamento}/pdf', [\App\Http\Controllers\Prontuario\EncaminhamentoController::class, 'pdf'])
                ->name('encaminhamentos.pdf');
        });

        // Financeiro — admin e perfil financeiro
        Route::middleware('can:acessar-financeiro')->prefix('financeiro')->name('financeiro.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Financeiro\FinanceiroController::class, 'index'])->name('index');
            Route::resource('contratos', \App\Http\Controllers\Financeiro\ContratoController::class);
            Route::post('contratos/{contrato}/gerar-cobranca',
                [\App\Http\Controllers\Financeiro\ContratoController::class, 'gerarCobranca'])
                ->name('contratos.gerar-cobranca');
            Route::resource('contas', \App\Http\Controllers\Financeiro\ContaReceberController::class);
            Route::match(['GET','POST'], 'contas/{conta}/parcelas/{parcela}/pagar',
                [\App\Http\Controllers\Financeiro\ParcelaController::class, 'pagar'])
                ->name('parcelas.pagar');
        });

        // LGPD — apenas admin
        Route::middleware('can:admin')->prefix('lgpd')->name('lgpd.')->group(function () {
            Route::get('audit', [\App\Http\Controllers\Lgpd\AuditController::class, 'index'])
                ->name('audit.index');
            Route::get('audit/paciente/{paciente}', [\App\Http\Controllers\Lgpd\AuditController::class, 'paciente'])
                ->name('audit.paciente');
            Route::get('export/paciente/{paciente}', [\App\Http\Controllers\Lgpd\ExportController::class, 'paciente'])
                ->name('export.paciente');
        });

        // Administração — apenas admin (Lucylady)
        Route::middleware('can:admin')->group(function () {
            Route::resource('profissionais', \App\Http\Controllers\Admin\ProfissionalController::class);
            Route::resource('usuarios', \App\Http\Controllers\Admin\UsuarioController::class);
            Route::get('admin/agenda-config', [\App\Http\Controllers\Admin\AgendaConfiguracaoController::class, 'index'])
                ->name('admin.agenda-config.index');
            Route::get('admin/agenda-config/{profissional}/edit', [\App\Http\Controllers\Admin\AgendaConfiguracaoController::class, 'edit'])
                ->name('admin.agenda-config.edit');
            Route::put('admin/agenda-config/{profissional}', [\App\Http\Controllers\Admin\AgendaConfiguracaoController::class, 'update'])
                ->name('admin.agenda-config.update');
            Route::get('admin/bloqueios', [\App\Http\Controllers\Admin\BloqueioController::class, 'index'])
                ->name('admin.bloqueios.index');
            Route::post('admin/bloqueios', [\App\Http\Controllers\Admin\BloqueioController::class, 'store'])
                ->name('admin.bloqueios.store');
            Route::delete('admin/bloqueios/{bloqueio}', [\App\Http\Controllers\Admin\BloqueioController::class, 'destroy'])
                ->name('admin.bloqueios.destroy');
        });
    });
});
