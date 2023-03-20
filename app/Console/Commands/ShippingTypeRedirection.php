<?php

namespace App\Console\Commands;

use App\Http\Controllers\accountStatements\GetFilePDFAccountStatementsController;
use App\Http\Controllers\FileValidatorEstadosCuentaController;
use App\Http\Controllers\PreparingShipmentController;
use Illuminate\Console\Command;

class ShippingTypeRedirection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ShippingType:redirection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de redireccionar al controlador apropiado respecto al tipo de envio nombrado en el argumento';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shippingType = 'Estado de Cuenta';

        //Array asociativo respecto al tipo de envío y ruta de ubicación
        $routesShipping = [
            'Estado de Cuenta' => [
                'pruebas' => '\\\\10.1.1.82\Simi\pdf\\Estados\\',
                'produccion' => '/mnt/server/',
                'controlador' => GetFilePDFAccountStatementsController::class
            ],
            'Certificados' => [
                'pruebas' => '\\\\10.1.1.82\Simi\pdf\\Certificado\\',
                'produccion' => '/mnt/server/',
                'controlador' => ''
            ]
        ];

        //Verifica si la clave del array existe
        if (!array_key_exists($shippingType, $routesShipping)) {
            return 'Tipo de envío no encontrado';
        }
        
        $sendRoute = new $routesShipping[$shippingType]['controlador'];
        $sendRoute->get_file($routesShipping[$shippingType], $shippingType);


        return Command::SUCCESS;
    }

}
