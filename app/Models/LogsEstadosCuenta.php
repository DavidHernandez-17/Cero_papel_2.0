<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogsEstadosCuenta extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'LogsEstadosCuenta';

    protected $fillable = ['id', 'mensaje', 'nombre_envio', 'remitente', 'destinatario', 'id_destinatario', 'asunto', 'wasEnviado', 'fechaEnvio', 'horaEnvio', 'nombreAdjunto'];
    
}
