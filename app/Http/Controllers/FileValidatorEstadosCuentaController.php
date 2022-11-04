<?php

namespace App\Http\Controllers;

use App\Models\Proprietary;
use Illuminate\Support\Facades\Mail;

class FileValidatorEstadosCuentaController extends Controller
{

    public function IdentificationValidator($file, $adjunto, $currentDirectory)
    {
        try
        {
            // Divido el nombre del archivo por '_'
            $separator = explode('_', $file);

            //Asigno la posición en la cual está el identificador (cédula o nit propietario), array 2, y le resta los 4 últimos caracteres
            $identification = substr($separator[2], 0, -4);

            //Consulta en la base de datos, SQL Server
            $searchers = Proprietary::where('IdCedula', '=', $identification)->limit(1)->get();

            //Si encuentra un registro relacionado con $identification
            if (sizeof($searchers) != 0 )
            {
                $logController = new LogsEstadosCuentaController(); //Define objeto controlador de registros en log

                //Correo electrónico de los propietarios
                foreach ($searchers as $searcher)
                {
                    //Atributos del propietario
                    $emailProprietary = $searcher->Email;
                    $nameProprietary = $searcher->Propietario;

                    //Valida si el propietario tiene correo electrónico
                    if( empty($emailProprietary) == true )
                    {
                        //Registro de log, correo electrónico no encontrado
                        $logController = new LogsEstadosCuentaController();
                        $logController->log_done(
                            'Correo electrónico no encontrado en base de datos',
                            'Estados de cuenta',
                            'null',
                            $identification,
                            'Estados de cuenta',
                            '0',
                            $file
                        );

                        echo('No tiene correo'. ' '.$identification."\n");
                    }
                    else
                    {
                        //Tomo el nombre del propietario y convierto en mayúscula el primer caracter de cada palabra de la cadena
                        $nameProprietaryConverted = ucwords(strtolower($nameProprietary));

                        //Realiza envío de correo electrónico, adjunto archivo relacionado.
                        $data["email"] = $emailProprietary;
                        $data["nameProprietary"] = $nameProprietaryConverted;

                        // Mail::send('EstadosCuenta.EstadoCuenta', $data, function ($message) use ($data, $adjunto, $file) {
                        //     $message->to($data["email"], $data["email"])
                        //         ->subject("Estados de cuenta")
                        //         ->attachData($adjunto, $file);                            
                        // });

                        //Registro de log exitoso
                        $logController->log_done(
                            'El correo fue enviado correctamente',
                            'Estados de cuenta',
                            $data["email"],
                            $identification,
                            'Estados de cuenta',
                            '1',
                            $file
                        );

                        //Mover archivo a carpeta EstadosCuentaEnviados
                        // $moveFile = new MoveFileController();
                        // $moveFile->movingFile($currentDirectory, $file);

                        //echo('Proceso realizado correctamente.' . ' ' . $identification . ' ' . $emailProprietary ."\n");
                    }
                }
            }
            else
            {
                //Registro de log en MySQL, identificación no encontrada
                $logController = new LogsEstadosCuentaController();
                $logController->log_done(
                    'Identificación no encontrada en base de datos',
                    'Estados de cuenta',
                    'null',
                    $identification,
                    'Estados de cuenta',
                    '0',
                    $file
                );
            }
        } 
        catch (\Throwable $th) 
        {
            //Registro de log, envio no realizado
            $logController = new LogsEstadosCuentaController();
            $logController->log_done(
                'Correo no enviado: ' . $th,
                'Estados de cuenta',
                'null',
                'null',
                'Estados de cuenta',
                '0',
                $file
            );
        }
    }
}
