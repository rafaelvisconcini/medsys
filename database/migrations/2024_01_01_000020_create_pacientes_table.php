<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->string('escola', 150)->nullable();
            $table->string('serie_escolar', 40)->nullable();
            $table->json('diagnosticos')->nullable(); // ["F84.0","F90.0"]
            $table->string('foto_path', 255)->nullable();
            $table->text('observacoes')->nullable();
            // Responsável (obrigatório)
            $table->string('responsavel_nome', 150);
            $table->string('responsavel_cpf', 11)->nullable();
            $table->string('responsavel_parentesco', 40);
            $table->string('responsavel_telefone', 15)->nullable();
            $table->string('responsavel_celular', 15);
            $table->string('responsavel_email', 120)->nullable();
            // Contato secundário
            $table->string('contato2_nome', 150)->nullable();
            $table->string('contato2_telefone', 15)->nullable();
            $table->string('contato2_parentesco', 40)->nullable();
            // Endereço
            $table->string('cep', 8)->nullable();
            $table->string('logradouro', 150)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('complemento', 60)->nullable();
            $table->string('bairro', 80)->nullable();
            $table->string('cidade', 80)->nullable();
            $table->char('uf', 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
