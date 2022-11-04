<?php

namespace App\Http\Controllers;

use App\Models\LogsEstadosCuenta;
class LogsEstadosCuentaController extends Controller
{
    public function log_done($mensaje, $nombre_envio, $destinatario, $id_destinatario, $asunto, $wasEnviado, $nombre_adjunto)
    {
        //Zona horaria
        date_default_timezone_set("America/Bogota");
        
        //Fecha actual fecha y hora junta
        $todayDateTime = date("Y-m-d H:i:s");

        //Fecha envio
        $fechaEnvioHoy = date("Y-m-d");

        //Hora envio
        $horaEnvioHoy = date("H:i:s");

        //Remitente
        $remitente = 'auxiliarcorrespondencia@albertoalvarez.com';

        try 
        {
            //Inserción en base de datos MySQL
            $log = new LogsEstadosCuenta();
            $log->mensaje = $mensaje;
            $log->nombre_envio = $nombre_envio;
            $log->remitente = $remitente;
            $log->destinatario = $destinatario;
            $log->id_destinatario = $id_destinatario;
            $log->asunto = $asunto;
            $log->wasEnviado = $wasEnviado;
            $log->fechaEnvio = $fechaEnvioHoy;
            $log->horaEnvio = $horaEnvioHoy;
            $log->nombre_adjunto = $nombre_adjunto;
            $log->updated_at = $todayDateTime;
            $log->created_at = $todayDateTime;
            $log->save();
        }
        catch (\Throwable $th) {
            echo($th . ' ');
        }
    }
}
