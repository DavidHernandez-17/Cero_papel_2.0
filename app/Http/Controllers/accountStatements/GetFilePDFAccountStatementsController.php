<?php

namespace App\Http\Controllers\accountStatements;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LogsEstadosCuentaController;
use App\Http\Controllers\SendEmailController;
use Illuminate\Http\Request;

class GetFilePDFAccountStatementsController extends Controller
{
    public function get_file($route, $shippingName){

        echo('Inicia proceso de envios '.$shippingName. "\n");

        $logController = new LogsEstadosCuentaController();  //El name controller se debe cambiar por LogsController

        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        //Año actual
        $currentYear = date("Y");
        
        //Mes actual
        $currentMonth = date("m"); 
        
        //Contadores
        $countFiles = 0;

        try 
        {
            //Invoco controlador para recorrer el mes 12 del año anterior, si el mes actual es 01
            $PreviousYear = new ReadPreviousYearController;
            $PreviousYear->PreviousYear($months, $route, $shippingName);
                        
            //Recorrer todas las carpetas del año actual, utilizando el array creado $months
            for ($i=0; $i < $currentMonth; $i++)
            {
                //Pruebas
                $currentFolder = opendir("{$route['pruebas']}{$currentYear}\\{$months[$i]}");  //Carpeta actual respecto al mes

                //Producción
                //$currentFolder = opendir("{$route['produccion']}{$currentYear}/{$months[$i]}"); //Carpeta actual respecto al mes

                //Recorremos los elementos de la carpeta actual
                while( $file = readdir($currentFolder)) 
                {
                    //Pruebas
                    $routeFile = "{$route['pruebas']}{$currentYear}\\{$months[$i]}\\{$file}";

                    //Producción
                    //$routeFile = "{$route['produccion']}{$currentYear}/{$months[$i]}/{$file}";

                    $ext = pathinfo($routeFile, PATHINFO_EXTENSION);

                    //Valida si el archivo es extensión pdf
                    if( $ext === 'pdf')
                    {
                        //Nombre del archivo
                        $baseName= pathinfo($routeFile, PATHINFO_BASENAME);

                        //Archivo adjunto
                        $attached = fopen($routeFile, "r");

                        // Divido el nombre del archivo por '_'
                        $separator = explode('_', $file);

                        //Obtengo identificación del cliente y le resta los 4 últimos caracteres
                        $identification = substr($separator[2], 0, -4);

                        // Definición de contador de archivo e invoco el controlador de envíos
                        $countFiles += 1;
                        echo($countFiles . ' ' . $baseName ."\n");
                        $send = new SendEmailController;
                        $send->send_any_type_email($baseName, $attached, $routeFile, $identification, $shippingName);
                    }
                }
            }
        } 
        catch (\Throwable $th) 
        {

            $logController->log_done(
                'Carpeta no existente'. $th,
                $shippingName,
                'null',
                'null',
                $shippingName,
                '0',
                'null'
            );

            echo($th);

        }

        echo('-> Cantidad de archivos pdf: '. number_format($countFiles, 0) . "\n");
        echo('Finaliza proceso de envios '.$shippingName);
    }
}
