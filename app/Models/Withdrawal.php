<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Withdrawal extends Model
{
    use LogsActivity;

    protected $fillable = [
        'amount', 'withdrawal_date', 'reason', 'purpose',
        'repay_date', 'repay_status', 'user_id', 'is_repaid',
    ];

    protected $dates = [
        'withdrawal_date',
        'repay_date',
    ];

    protected function casts(): array
    {
        return [
            'withdrawal_date' => 'date',
            'repay_date' => 'date',
        ];
    }

    /**
     * Relacionamento: este saque foi feito por um usuário (admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'reason', 'purpose', 'repay_date', 'repay_status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('saques')
            ->setDescriptionForEvent(fn (string $eventName) => "Saque foi {$eventName}");
    }

    // Evento: quando o status muda para "Reposto", marca como devolvido
    protected static function booted()
    {
        static::updating(function ($withdrawal) {
            // Se mudou para "Reposto" e ainda não foi marcado como repaid
            if ($withdrawal->isDirty('repay_status') &&
                $withdrawal->repay_status === 'Reposto' &&
                ! $withdrawal->is_repaid) {

                $withdrawal->is_repaid = true;
            }
        });
    }
}
