<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsEstadosCuenta extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; //Conexión bd cero papel
    protected $table = 'logs_estados_cuenta';

    protected $fillable = ['id', 'mensaje', 'nombre_envio', 'remitente', 'asunto', 'es_Enviado', 'nombreAdjunto', 'fechaEnvio', 'horaEnvio', 'destinatario', 'id_destinatario'];
    
}
