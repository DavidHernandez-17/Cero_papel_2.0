<?php

namespace App\Http\Controllers;

use App\Models\LogsEstadosCuenta;
class LogsEstadosCuentaController extends Controller
{
    public function log_done($mensaje, $nombre_envio, $destinatario, $id_destinatario, $asunto, $es_enviado, $nombre_adjunto)
    {
        //Zona horaria
        date_default_timezone_set("America/Bogota");
        
        //Fecha actual fecha y hora junta
        $todayDateTime = date("Y-m-d H:i:s");

        //Fecha envio
        $dateToday = date("Y-m-d");

        //Hora envio
        $timeToday = date("H:i:s");

        //Remitente
        $remitente = 'auxiliarcorrespondencia@albertoalvarez.com';

        try 
        {
            //Inserción en base de datos MySQL
            $log = new LogsEstadosCuenta();
            $log->mensaje = $mensaje;
            $log->nombre_envio = $nombre_envio;
            $log->remitente = $remitente;
            $log->asunto = $asunto;
            $log->nombre_adjunto = $nombre_adjunto;
            $log->es_enviado = $es_enviado;
            $log->fecha_envio = $dateToday;
            $log->hora_envio = $timeToday;
            $log->destinatario = $destinatario;
            $log->id_destinatario = $id_destinatario;
            $log->updated_at = $todayDateTime;
            $log->created_at = $todayDateTime;
            $log->save();
        }
        catch (\Throwable $th) {
            echo($th . ' ');
        }
    }
}
