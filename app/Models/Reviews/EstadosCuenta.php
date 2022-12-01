<?php

namespace App\Models\Reviews;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosCuenta extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; //Conexión bd cero papel
    protected $table = 'estados_cuentas';

    protected $fillable = [
        'archivo_es_creado',
        'es_enviado',
        'id_destinatario',
        'id_log'
    ];
}
