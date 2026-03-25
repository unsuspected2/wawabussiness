<?php

namespace App\Traits;

use App\Models\MonthlyCashClosure;
use Carbon\Carbon;

trait ChecksCashClosure
{
    /**
     * Verifica se o mês de uma data já foi fechado
     */
    protected function monthIsClosed($date): bool
    {
        if (! $date) {
            return false;
        }

        $yearMonth = Carbon::parse($date)->format('Y-m');

        return MonthlyCashClosure::where('year_month', $yearMonth)->exists();
    }

    /**
     * Retorna mensagem de erro padrão
     */
    protected function closedMonthMessage(): string
    {
        return 'Não é possível realizar esta ação. O mês já foi fechado no caixa mensal.';
    }
}
