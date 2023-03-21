<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MoveFileController extends Controller
{
    public function movingFile($routeFile, $baseName, $shippingName)
    {
        date_default_timezone_set("America/Bogota");

        //Definición año actual
        $currentYear = date("Y");

        //Definición mes actual
        $currentMonth = date("m");

        //Definición fecha actual
        $todayDate = date("Y-m-d"); 

        $origin = $routeFile;

        $movementPath = [
            'Estados de Cuenta' => [
                'destination_test' => "\\\\10.1.1.82\Simi\pdf\\Estados\\EstadosCuentaEnviados\\{$currentYear}\\{$currentMonth}\\{$todayDate}",
                'destination_production' => "/mnt/server/EstadosCuentaEnviados/{$currentYear}/{$currentMonth}/{$todayDate}"
            ],
            'Certificados' => [
                'destination_test' => "\\\\10.1.1.82\Simi\pdf\\Certificado\\CertificadosEnviados\\{$todayDate}",
                'destination_production' => "/mnt/server/{$todayDate}",
            ]
        ];

        try
        {
            $destination = $movementPath[$shippingName]['destination_test'];

            //Validar si la carpeta existe con fecha actual
            if (is_dir($destination)) 
            {
                rename($origin, $destination . '/' . $baseName);
            } 
            else
            {
                mkdir($destination);
                rename($origin, $destination . '/' . $baseName);
            }
        } 
        catch (\Throwable $th) 
        {
            echo('Error, ruta de archivo no modificada '. $th ."\n");

            //Registro de log movimiento de carpeta no realizado
            $logController = new LogsEstadosCuentaController();
            $logController->log_done(
                'La ubicación del archivo '.$baseName.' no fue modificada correctamente '. $th,
                $shippingName,
                'null',
                'null',
                'null',
                '1',
                $baseName
            );
        }
    }
}
