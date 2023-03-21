<?php

namespace App\Http\Controllers\accountStatements;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileValidatorEstadosCuentaController;
use App\Http\Controllers\SendEmailController;
use Illuminate\Http\Request;

class ReadPreviousYearController extends Controller
{
    public function PreviousYear($months, $route, $shippingName)
    {
        if( date("m") == '01' || date("m") == '1')
        {
            //Defino el año anterior
            $previousYear = date('Y') - 1;

            //Contador
            $countFiles = 0;

            for ($i = 11; $i == 11; $i++)
            {
                //Pruebas
                //$currentFolder = opendir("{$route['pruebas']}{$previousYear}\\{$months[$i]}");  //Carpeta actual respecto al mes

                //Producción
                $currentFolder = opendir("{$route['produccion']}{$previousYear}/{$months[$i]}"); //Carpeta actual respecto al mes

                //Recorremos los elementos de la carpeta actual
                while( $file = readdir($currentFolder)) 
                {
                    //Pruebas
                    //$routeFile = "{$route['pruebas']}{$previousYear}\\{$months[$i]}\\{$file}";

                    //Producción
                    $routeFile = "{$route['produccion']}{$previousYear}/{$months[$i]}/{$file}";

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
                        //echo($countFiles . ' ' . $baseName ."\n");
                        $send = new SendEmailController;
                        $send->send_any_type_email($baseName, $attached, $routeFile, $identification, $shippingName);
                    }
                }
            }

            echo('-> Cantidad de archivos pdf año y mes anterior: '. number_format($countFiles, 0) . "\n");
        }
    }
}
