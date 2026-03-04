<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('service');
            $table->string('plan');
            $table->decimal('value_paid', 8, 2);
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('status', ['Ativo', 'Vencido', 'Cancelado'])->default('Ativo');
            $table->text('observations')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
