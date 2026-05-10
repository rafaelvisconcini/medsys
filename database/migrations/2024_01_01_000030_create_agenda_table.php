<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('profissionais')->cascadeOnDelete();
            $table->tinyInteger('dia_semana'); // 0=Dom, 1=Seg ... 6=Sab
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->unsignedSmallInteger('duracao_slot_min')->default(50);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('agenda_bloqueios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('profissionais')->cascadeOnDelete();
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim');
            $table->string('motivo', 120)->nullable();
            $table->timestamps();
        });

        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profissional_id')->constrained('profissionais')->cascadeOnDelete();
            $table->enum('especialidade', [
                'fisioterapia', 'fonoaudiologia', 'psicologia',
                'psicopedagogia', 'terapia_ocupacional',
            ]);
            $table->dateTime('data_hora');
            $table->unsignedSmallInteger('duracao_min')->default(50);
            $table->enum('tipo', ['sessao', 'avaliacao', 'reuniao_familia', 'reposicao'])->default('sessao');
            $table->enum('status', ['agendada', 'confirmada', 'realizada', 'cancelada', 'faltou', 'reposicao'])->default('agendada');
            $table->string('motivo_cancelamento', 200)->nullable();
            $table->boolean('gera_cobranca')->default(true);
            $table->unsignedBigInteger('contrato_id')->nullable(); // FK adicionada após criação de contratos
            $table->foreignId('agendado_por')->constrained('users');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['profissional_id', 'data_hora']);
            $table->index(['paciente_id', 'data_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes');
        Schema::dropIfExists('agenda_bloqueios');
        Schema::dropIfExists('agenda_configuracoes');
    }
};
