<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MonthlyCashClosure extends Model
{
    use LogsActivity;

    protected $fillable = [
        'year_month',
        'closed_at',
        'user_id',
        'starting_balance',
        'total_inflows',
        'total_outflows',
        'total_expenses',
        'notes',
        'status',
    ];

    protected $casts = [
        'closed_at'        => 'datetime',
        'starting_balance' => 'decimal:2',
        'total_inflows'    => 'decimal:2',
        'total_outflows'   => 'decimal:2',
        'total_expenses'   => 'decimal:2',
        'ending_balance'   => 'decimal:2',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'year_month', 
                'starting_balance', 
                'total_inflows', 
                'total_outflows', 
                'total_expenses', 
                'ending_balance', 
                'notes', 
                'status'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('caixa_fechamento')
            ->setDescriptionForEvent(function(string $eventName) {
                return match($eventName) {
                    'created' => 'Fechou o caixa do mês ' . $this->year_month,
                    'updated' => 'Atualizou o fechamento do caixa ' . $this->year_month,
                    'deleted' => 'Removeu o fechamento do caixa ' . $this->year_month,
                    default   => 'Realizou ação no fechamento de caixa',
                };
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function formattedMonth(): Attribute
    {
        return Attribute::make(
            get: fn () => \Carbon\Carbon::createFromFormat('Y-m', $this->year_month)->format('F Y')
        );
    }
}