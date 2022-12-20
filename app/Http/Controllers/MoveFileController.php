<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MoveFileController extends Controller
{
    public function movingFile($currentLocation, $nameFile)
    {
        //Definición de hora local Colombiana
        date_default_timezone_set("America/Bogota");

        //Definición año actual
        $currentYear = date("Y");

        //Definición mes actual
        $currentMonth = date("m");

        //Definición fecha actual
        $todayDate = date("Y-m-d"); 

        $origin = $currentLocation;

        // Para pruebas
        // $destination = "\\\\10.1.1.82\Simi\pdf\\Estados\\EstadosCuentaEnviados\\{$currentYear}\\{$currentMonth}\\{$todayDate}";

        // Para producción
        $destination = "/mnt/server/EstadosCuentaEnviados/{$currentYear}/{$currentMonth}/{$todayDate}";

        try 
        {
            //Validar si la carpeta existe con fecha actual
            if (is_dir($destination)) 
            {
                $moved = rename($origin, $destination . '\\' . $nameFile);
            } 
            else
            {
                mkdir($destination);
                $moved = rename($origin, $destination . '\\' . $nameFile);
            }
        } 
        catch (\Throwable $th) 
        {
            echo('Error, ruta de archivo no modificada '. "\n");

            //Registro de log movimiento de carpeta no realizado
            $logController = new LogsEstadosCuentaController();
            $logController->log_done(
                'La ubicación del archivo '.$nameFile.' no fue modificada correctamente '. $th,
                'Estados de cuenta',
                'null',
                'null',
                'Estados de cuenta',
                '1',
                $nameFile
            );
        }
    }
}
