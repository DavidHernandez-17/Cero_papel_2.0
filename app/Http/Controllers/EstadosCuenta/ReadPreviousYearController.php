<?php

namespace App\Http\Controllers\EstadosCuenta;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileValidatorEstadosCuentaController;
use Illuminate\Http\Request;

class ReadPreviousYearController extends Controller
{
    public function PreviousYear($meses)
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
                //$currentFolder = opendir("\\\\10.1.1.82\Simi\pdf\\Estados\\{$previousYear}\\{$meses[$i]}");

                //Producción
                $currentFolder = opendir("/mnt/server/{$previousYear}/{$meses[$i]}"); //Carpeta mes 12 mes anterior

                //Recorremos los elementos de la carpeta actual
                while( $file = readdir($currentFolder)) 
                {
                    //Pruebas
                    //$routeFile = "\\\\10.1.1.82\Simi\pdf\\Estados\\{$previousYear}\\{$meses[$i]}\\{$file}";

                    //Producción
                    $routeFile = "/mnt/server/{$previousYear}/{$meses[$i]}/{$file}";

                    $ext = pathinfo($routeFile, PATHINFO_EXTENSION);

                    //Valida si el archivo es extensión pdf
                    if( $ext === 'pdf')
                    {
                        //Nombre del archivo
                        $baseName= pathinfo($routeFile, PATHINFO_BASENAME);
                        
                        //Archivo adjunto
                        $adjunto = fopen($routeFile, "r");

                        // Definición de contador de archivo e invoco al validador de archivos
                        $countFiles += 1;
                        //echo($countFiles . ' ' . $baseName ."\n");
                        $fileValidator = new FileValidatorEstadosCuentaController;
                        $fileValidator->IdentificationValidator($baseName, $adjunto, $routeFile);
                    }
                }
            }

            echo('-> Cantidad de archivos pdf año anterior'. number_format($countFiles, 0) . "\n");
        }
    }
}
