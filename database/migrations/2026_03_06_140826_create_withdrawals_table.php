<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->dateTime('withdrawal_date');
            $table->text('reason');
            $table->text('purpose')->nullable();
            $table->date('repay_date')->nullable();
            $table->enum('repay_status', ['Pendente', 'Reposto', 'Não vai repor'])->default('Pendente');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
