<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prontuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('numero', 20)->unique();
            $table->timestamps();
        });

        Schema::create('planos_terapeuticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->date('periodo_inicio');
            $table->date('periodo_fim')->nullable();
            $table->enum('status', ['ativo', 'finalizado', 'suspenso'])->default('ativo');
            $table->foreignId('criado_por')->constrained('users');
            $table->timestamps();
        });

        Schema::create('plano_terapeutico_especialidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_terapeutico_id')->constrained('planos_terapeuticos')->cascadeOnDelete();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->enum('especialidade', [
                'fisioterapia', 'fonoaudiologia', 'psicologia',
                'psicopedagogia', 'terapia_ocupacional',
            ]);
            $table->text('objetivos_gerais')->nullable();
            $table->text('objetivos_especificos')->nullable();
            $table->text('estrategias')->nullable();
            $table->foreignId('atualizado_por')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('evolucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prontuario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sessao_id')->nullable()->constrained('sessoes')->nullOnDelete();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->enum('especialidade', [
                'fisioterapia', 'fonoaudiologia', 'psicologia',
                'psicopedagogia', 'terapia_ocupacional',
            ]);
            $table->dateTime('data_hora');
            $table->text('descricao')->nullable();
            $table->text('objetivos_trabalhados')->nullable();
            $table->text('resposta_paciente')->nullable();
            $table->text('proximos_objetivos')->nullable();
            $table->json('cids')->nullable(); // ["F84.0","F90.0"]
            $table->timestamps();
        });

        Schema::create('encaminhamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prontuario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->string('para_especialidade', 100);
            $table->text('motivo');
            $table->date('data');
            $table->enum('status', ['pendente', 'realizado', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('prontuario_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prontuario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evolucao_id')->nullable()->constrained('evolucoes')->nullOnDelete();
            $table->enum('tipo', ['avaliacao', 'laudo', 'relatorio', 'imagem', 'outro']);
            $table->string('nome_original', 255);
            $table->string('path', 500);
            $table->string('mime_type', 80)->nullable();
            $table->unsignedInteger('tamanho_bytes')->nullable();
            $table->string('descricao', 200)->nullable();
            $table->date('data_documento')->nullable();
            $table->foreignId('uploaded_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prontuario_anexos');
        Schema::dropIfExists('encaminhamentos');
        Schema::dropIfExists('evolucoes');
        Schema::dropIfExists('plano_terapeutico_especialidades');
        Schema::dropIfExists('planos_terapeuticos');
        Schema::dropIfExists('prontuarios');
    }
};
