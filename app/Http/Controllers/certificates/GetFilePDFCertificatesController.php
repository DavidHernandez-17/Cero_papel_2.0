<?php

namespace App\Http\Controllers\certificates;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogsEstadosCuentaController;
use App\Http\Controllers\SendEmailController;
use Illuminate\Http\Request;

class GetFilePDFCertificatesController extends Controller
{
    public function get_file($route, $shippingName)
    {

        echo ('Inicia proceso de envios ' . $shippingName . "\n");

        $logController = new LogsEstadosCuentaController();  //El name controller se debe cambiar por LogsController

        //Contadores
        $countFiles = 0;

        try {

            //Pruebas
            $currentFolder = opendir("{$route['pruebas']}");  //Carpeta actual respecto al mes

            //Producción
            //$currentFolder = opendir("{$route['produccion']}"); //Carpeta actual respecto al mes

            //Recorremos los elementos de la carpeta actual
            while ($file = readdir($currentFolder)) {
                //Pruebas
                $routeFile = "{$route['pruebas']}\\{$file}";

                //Producción
                //$routeFile = "{$route['produccion']}/{$file}";

                $ext = pathinfo($routeFile, PATHINFO_EXTENSION);

                //Valida si el archivo es extensión pdf
                if ($ext === 'pdf') {
                    //Nombre del archivo
                    $baseName = pathinfo($routeFile, PATHINFO_BASENAME);

                    //Archivo adjunto
                    $attached = fopen($routeFile, "r");

                    // Divido el nombre del archivo por '_'
                    $separator = explode('_', $file);

                    //Obtengo identificación del cliente y le resta los 4 últimos caracteres
                    $identification = substr($separator[3], 0, -4);

                    // Definición de contador de archivo e invoco el controlador de envíos
                    $countFiles += 1;
                    //echo ($countFiles . ' ' . $baseName . ' - '. $identification ."\n");
                    $send = new SendEmailController;
                    $send->send_any_type_email($baseName, $attached, $routeFile, $identification, $shippingName);
                }
            }
        } 
        catch (\Throwable $th) {

            $logController->log_done(
                'Carpeta no existente'. $th,
                $shippingName,
                'null',
                'null',
                'null',
                '0',
                'null'
            );

            echo ($th);
        }

        echo ('-> Cantidad de archivos pdf: ' . number_format($countFiles, 0) . "\n");
        echo ('Finaliza proceso de envios ' . $shippingName);
    }
}
