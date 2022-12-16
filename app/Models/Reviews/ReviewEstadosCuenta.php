<?php

namespace App\Models\Reviews;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewEstadosCuenta extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; //Conexión bd cero papel
    protected $table = 'review_estados_cuenta';

    protected $fillable = [
        'archivo_es_creado',
        'es_enviado',
        'id_destinatario',
        'id_log'
    ];
}
