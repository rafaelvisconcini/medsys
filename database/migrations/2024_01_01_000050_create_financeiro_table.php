<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profissional_id')->constrained('profissionais');
            $table->enum('especialidade', [
                'fisioterapia', 'fonoaudiologia', 'psicologia',
                'psicopedagogia', 'terapia_ocupacional',
            ]);
            $table->decimal('valor_mensal', 10, 2);
            $table->tinyInteger('dia_vencimento')->default(10); // 1-28
            $table->tinyInteger('sessoes_por_semana')->default(2);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->enum('status', ['ativo', 'suspenso', 'encerrado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained();
            $table->foreignId('sessao_id')->nullable()->constrained('sessoes')->nullOnDelete();
            $table->foreignId('contrato_id')->nullable()->constrained()->nullOnDelete();
            $table->string('descricao', 200);
            $table->decimal('valor_total', 10, 2);
            $table->enum('tipo', ['mensalidade', 'avulso'])->default('avulso');
            $table->enum('status', ['pendente', 'parcial', 'quitado', 'cancelado'])->default('pendente');
            $table->date('data_vencimento');
            $table->date('data_liquidacao')->nullable();
            $table->date('referencia_mes')->nullable(); // para mensalidades
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conta_receber_id')->constrained('contas_receber')->cascadeOnDelete();
            $table->tinyInteger('numero_parcela')->default(1);
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('forma_pagamento', [
                'dinheiro', 'pix', 'cartao_debito', 'cartao_credito', 'transferencia', 'outro',
            ])->nullable();
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->string('comprovante_path', 500)->nullable();
            $table->string('observacoes', 200)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Adiciona FK de sessoes->contratos agora que contratos existe
        Schema::table('sessoes', function (Blueprint $table) {
            $table->foreign('contrato_id')->references('id')->on('contratos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sessoes', function (Blueprint $table) {
            $table->dropForeign(['contrato_id']);
        });
        Schema::dropIfExists('parcelas');
        Schema::dropIfExists('contas_receber');
        Schema::dropIfExists('contratos');
    }
};
