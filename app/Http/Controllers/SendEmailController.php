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
                            'null',
                            '0',
                            $baseName
                        );

                    } else {
                        //Tomo el nombre del cliente y convierto en mayúscula el primer caracter de cada palabra de la cadena
                        $nameClientConverted = ucwords(strtolower($nameClient));

                        //Características del envío
                        $shippingCharacteristics = [
                            'Estados de Cuenta' => [
                                'subject' => "Estado de cuenta - {$identification}",
                                'email_body' => 'AccountStatements.body'
                            ],
                            'Certificados' => [
                                'subject' => "Certificados de ingresos - {$identification}",
                                'email_body' => 'Certificates.body'
                            ]
                        ];

                        $data["email"] = $emailClient;
                        $data["nameClient"] = $nameClientConverted;
                        $data["subject"] = $shippingCharacteristics[$shippingName]['subject'];
                        $data["emailBody"] = $shippingCharacteristics[$shippingName]['email_body'];

                        Mail::send($data["emailBody"], $data, function ($message) use ($data, $attached, $baseName) {
                            $message->to($data["email"], $data["email"])
                                ->subject($data["subject"])
                                ->attachData($attached, $baseName);
                        });

                        //Registro de log exitoso
                        $logController->log_done(
                            'El correo fue enviado correctamente',
                            $shippingName,
                            $data["email"],
                            $identification,
                            $data["subject"],
                            '1',
                            $baseName
                        );

                        //Mover archivo a carpeta EstadosCuentaEnviados
                        $moveFile = new MoveFileController();
                        $moveFile->movingFile($routeFile, $baseName, $shippingName);

                        //echo('Proceso realizado correctamente.' . ' ' . $identification . ' ' . $emailClient . "\n");
                    }
                }
            } else {
                $logController->log_done(
                    'Identificación no encontrada en base de datos',
                    $shippingName,
                    'null',
                    $identification,
                    'null',
                    '0',
                    $baseName
                );

                //echo('Identificación no encontrada en base de datos');
            }
        } catch (\Throwable $th) {
            //Registro de log, envio no realizado
            $logController->log_done(
                'Correo no enviado: ' . $th,
                $shippingName,
                'null',
                'null',
                'null',
                '0',
                $baseName
            );
        }
    }
}
