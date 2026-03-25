<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_cash_closures', function (Blueprint $table) {
            $table->id();
            $table->string('year_month', 7)->unique();          // ex: '2026-03'
            $table->dateTime('closed_at');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('starting_balance', 15, 2)->default(0.00);
            $table->decimal('total_inflows', 15, 2)->default(0.00);     // entradas (pagamentos)
            $table->decimal('total_outflows', 15, 2)->default(0.00);    // saques
            $table->decimal('total_expenses', 15, 2)->default(0.00);    // despesas (Netflix, etc.)
            $table->decimal('ending_balance', 15, 2)->virtualAs('starting_balance + total_inflows - total_outflows - total_expenses');
            $table->text('notes')->nullable();
            $table->enum('status', ['closed', 'reopened'])->default('closed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_cash_closures');
    }
};