<?php

namespace App\Console\Commands;

use App\Http\Controllers\EstadosCuenta\ReadPreviousYearController;
use App\Http\Controllers\FileValidatorEstadosCuentaController;
use App\Http\Controllers\LogsEstadosCuentaController;
use Illuminate\Console\Command;


class SendEmailProprietary extends Command
{
    protected $signature = 'SendEmail:EstadosCuenta';

    protected $description = 'Enviar correos electrónicos a propietarios relacionados con los estados de cuenta';

    public function handle()
    {
        $this->info('Inicia proceso de envios, estados de cuenta.'. "\n");

        $meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

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
            $PreviousYear->PreviousYear($meses);
                        
            //Recorrer todas las carpetas del año actual, utilizando el array creado $meses
            for ($i=0; $i < $currentMonth; $i++)
            { 
                
                //Pruebas
                // $currentFolder = opendir("\\\\10.1.1.82\Simi\pdf\\Estados\\{$currentYear}\\{$meses[$i]}");  //Carpeta actual respecto al mes

                //Producción
                $currentFolder = opendir("/mnt/server/{$currentYear}/{$meses[$i]}"); //Carpeta actual respecto al mes

                //Recorremos los elementos de la carpeta actual
                while( $file = readdir($currentFolder)) 
                {
                    //Pruebas
                    // $routeFile = "\\\\10.1.1.82\Simi\pdf\\Estados\\{$currentYear}\\{$meses[$i]}\\{$file}";

                    //Producción
                    $routeFile = "/mnt/server/{$currentYear}/{$meses[$i]}/{$file}";

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
                        // echo($countFiles . ' ' . $baseName ."\n");
                        $fileValidator = new FileValidatorEstadosCuentaController;
                        // $fileValidator->IdentificationValidator($baseName, $adjunto, $routeFile);
                    }
                }
            }
        } 
        catch (\Throwable $th) 
        {
            //Registro de log exitoso
            $logController = new LogsEstadosCuentaController();
            $logController->log_done(
                'Carpeta no existente'. $th,
                'Estados de cuenta',
                'null',
                'null',
                'Estados de cuenta',
                '0',
                'null'
            );

            echo($th);

        }

        $this->info('-> Cantidad de archivos pdf: '. number_format($countFiles, 0) . "\n");
        $this->info('Finaliza proceso de envios, estados de cuenta.');

        return Command::SUCCESS;
    }
}
