<?php

namespace App\Http\Controllers;

use App\Models\UnionTables\Owner_Coowner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendEmailController extends Controller
{
    public function send_any_type_email($baseName, $attached, $routeFile, $identification, $shippingName)
    {
        $logController = new LogsEstadosCuentaController();

        try {

            $findClients = Owner_Coowner::where('ced_cliente', $identification)->limit(1)->get();

            //Si encuentra un cliente relacionado
            if (sizeof($findClients) != 0) {

                //Correo electrónico de los clientes
                foreach ($findClients as $client) {

                    $emailClient = $client['email_cliente'];
                    $nameClient = $client['nombre_cliente'];

                    //Valida si el cliente tiene correo electrónico
                    if (empty($emailClient) == true) {
                        //Registro de log, correo electrónico no encontrado
                        $logController->log_done(
                            'Correo electrónico no encontrado en base de datos',
                            $shippingName,
                            'null',
                            $identification,
                            $shippingName,
                            '0',
                            $baseName
                        );

                        //echo('No tiene correo'. ' '.$identification."\n");
                    } else {
                        //Tomo el nombre del cliente y convierto en mayúscula el primer caracter de cada palabra de la cadena
                        $nameClientConverted = ucwords(strtolower($nameClient));

                        //Realiza envío de correo electrónico, adjunto archivo relacionado.
                        $data["email"] = $emailClient;
                        $data["nameClient"] = $nameClientConverted;
                        $data["identificacionCliente"] = $shippingName.' - '. $identification;

                        Mail::send('EstadosCuenta.EstadoCuenta', $data, function ($message) use ($data, $attached, $baseName) {
                            $message->to($data["email"], $data["email"])
                                ->subject($data["identificacionCliente"])
                                ->attachData($attached, $baseName);
                        });

                        //Registro de log exitoso
                        // $logController->log_done(
                        //     'El correo fue enviado correctamente',
                        //     $shippingName,
                        //     $data["email"],
                        //     $identification,
                        //     $shippingName,
                        //     '1',
                        //     $attached
                        // );

                        //Mover archivo a carpeta EstadosCuentaEnviados
                        // $moveFile = new MoveFileController();
                        // $moveFile->movingFile($routeFile, $attached, $shippingName);

                        dd('Proceso realizado correctamente.' . ' ' . $identification . ' ' . $emailClient ."\n");
                    }
                }
            } else {
                    $logController->log_done(
                    'Identificación no encontrada en base de datos',
                    $shippingName,
                    'null',
                    $identification,
                    $shippingName,
                    '0',
                    $attached
                );
            }
        } catch (\Throwable $th) {
            //Registro de log, envio no realizado
            $logController = new LogsEstadosCuentaController();
            $logController->log_done(
                'Correo no enviado: ' . $th,
                $shippingName,
                'null',
                'null',
                $shippingName,
                '0',
                $attached
            );
        }
    }
}
