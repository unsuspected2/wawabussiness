<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'whatsapp', 'service', 'plan', 'value_paid',
        'start_date', 'due_date', 'status', 'observations'
    ];

    // Atualiza status automaticamente
    public function save(array $options = [])
    {
        $this->updateStatus();
        parent::save($options);
    }

    public static function boot()
    {
        parent::boot();
        static::retrieved(function ($client) {
            $client->updateStatus();
            $client->save();
        });
    }

    protected function updateStatus()
    {
        if ($this->due_date < now()->format('Y-m-d') && $this->status !== 'Cancelado') {
            $this->status = 'Vencido';
        }
    }
}
