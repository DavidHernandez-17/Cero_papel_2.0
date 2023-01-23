<?php

namespace App\Console\Commands\UnionTables;

use App\Models\UnionTables\Owner;
use App\Models\UnionTables\Owner_Coowner;
use Illuminate\Console\Command;

class OwnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UnionTable:owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de registrar información de los propietarios cada día (Los toma de SQl server)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo('Inicia registro de propietarios actuales'. "\n");

        //Zona horaria
        date_default_timezone_set("America/Bogota");

        //Fecha actual fecha y hora junta
        $todayDateTime = date("Y-m-d H:i:s");

        //Contadores
        $countNews = 0;
        $countUpdate = 0;

        try 
        {
            //Consulta todos los registros de pp en bd sql server
            $currentOwners = Owner::all();
            
            foreach ($currentOwners as $currentOwner)
            {
                //Captura la cédula y el email del co-propietario
                $cedula = $currentOwner['Ced. Prop.'];
                $email = trim( $currentOwner['E-mail Prop.'] );

                //Consulto cédula en base de datos mySQL
                $foundCedulas = Owner_Coowner::where('ced_cliente', $cedula)->get();

                //Cedula encontrada
                if( sizeof($foundCedulas) == 1  )
                {
                    //Recorro los registros de la tabla con modelo Owner_Coowner
                    foreach ($foundCedulas as $foundCedula) {

                        //Id del registro que va actualizar
                        $id = $foundCedula['id'];

                        //Actualiza información
                        $updateOwner = Owner_Coowner::findOrfail($id);
                        $updateOwner->id_inmueble = $currentOwner['Inmueble'];
                        $updateOwner->ced_cliente = $cedula;
                        $updateOwner->nombre_cliente = $currentOwner['Propietario'];
                        $updateOwner->email_cliente = $email;
                        $updateOwner->tipo_cliente = "Propietario";
                        $updateOwner->created_at = $todayDateTime;
                        $updateOwner->updated_at = $todayDateTime;
                        $updateOwner->save();
                        $countUpdate += 1;
                    }
                }
                else
                {
                    //Crea objeto para insertar datos en tabla cero papel
                    $registerOwner = new Owner_Coowner();

                    $registerOwner->id_inmueble = $currentOwner['Inmueble'];
                    $registerOwner->ced_cliente = $currentOwner['Ced. Prop.'];
                    $registerOwner->nombre_cliente = $currentOwner['Propietario'];
                    $registerOwner->email_cliente = $email;
                    $registerOwner->tipo_cliente = "Propietario";
                    $registerOwner->created_at = $todayDateTime;
                    $registerOwner->updated_at = $todayDateTime;
                    $registerOwner->save();
                    $countNews += 1;
                }
            }

            $this->info('Finaliza registro de propietarios actuales'."\n");
            $this->info('Cantidad pp actualizados: ' . $countUpdate . "\n");
            $this->info('Cantidad pp registrados: ' . $countNews . "\n");



            return Command::SUCCESS;
            
        } 
        catch (\Throwable $th) 
        {
            echo( $th );
        }
    }
}
