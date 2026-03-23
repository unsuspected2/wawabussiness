<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilAlocado extends Model
{
    use HasFactory;

    protected $table = 'perfis_alocados';

    protected $fillable = [
        'client_id', 'payment_id', 'service_id', 'tipo_alocacao',
        'nome_perfil', 'email_conta', 'senha_conta', 'login_perfil', 'senha_perfil',
        'status', 'observacao'
    ];

    public function cliente()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function pagamento()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function servico()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}