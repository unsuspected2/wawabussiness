<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfis_alocados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('restrict');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->enum('tipo_alocacao', ['perfil', 'pessoal'])->nullable();
            $table->string('nome_perfil', 100)->nullable();          // só pra 'perfil'
            $table->string('email_conta', 255)->nullable();          // só se 'pessoal'
            $table->string('senha_conta', 255)->nullable();          // só se 'pessoal'
            $table->string('login_perfil', 100)->nullable();         // só pra 'perfil'
            $table->string('senha_perfil', 100)->nullable();         // só pra 'perfil'
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfis_alocados');
    }
};
