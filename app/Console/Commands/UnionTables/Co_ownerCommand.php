<?php

namespace App\Console\Commands\UnionTables;

use App\Models\UnionTables\Co_owner;
use App\Models\UnionTables\Owner_Coowner;
use Illuminate\Console\Command;

class Co_ownerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UnionTable:co_owner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de registrar información de los co-propietarios cada día (Los toma de SQl server)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo('Inicia registro de co-propietarios actuales'. "\n");

        //Zona horaria
        date_default_timezone_set("America/Bogota");

        //Fecha actual fecha y hora junta
        $todayDateTime = date("Y-m-d H:i:s");

        //Contadores
        $countNews = 0;
        $countUpdate = 0;

        try 
        {
            //Consulta todos los registros de co-pp en bd sql server
            $currentCo_Owners = Co_owner::all();
            
            foreach ($currentCo_Owners as $currentCo_Owner)
            {
                //Captura la cédula y el email del co-propietario
                $cedula = $currentCo_Owner['IdCedula'];
                $email = trim( $currentCo_Owner['Email'] );

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
                        $updateCo_Owner = Owner_Coowner::findOrfail($id);
                        $updateCo_Owner->id_inmueble = $currentCo_Owner['IdInmueble'];
                        $updateCo_Owner->ced_cliente = $currentCo_Owner['IdCedula'];
                        $updateCo_Owner->nombre_cliente = $currentCo_Owner['Propietario'];
                        $updateCo_Owner->email_cliente = $email;
                        $updateCo_Owner->tipo_cliente = "Co-Propietario";
                        $updateCo_Owner->created_at = $todayDateTime;
                        $updateCo_Owner->updated_at = $todayDateTime;
                        $updateCo_Owner->save();
                        $countUpdate += 1;
                    }
                }
                else
                {
                    //Crea objeto para insertar datos en tabla cero papel Owner_Coowner
                    $registerCo_Owner = new Owner_Coowner();

                    $registerCo_Owner->id_inmueble = $currentCo_Owner['IdInmueble'];
                    $registerCo_Owner->ced_cliente = $currentCo_Owner['IdCedula'];
                    $registerCo_Owner->nombre_cliente = $currentCo_Owner['Propietario'];
                    $registerCo_Owner->email_cliente = $email;
                    $registerCo_Owner->tipo_cliente = "Co-Propietario";
                    $registerCo_Owner->created_at = $todayDateTime;
                    $registerCo_Owner->updated_at = $todayDateTime;
                    $registerCo_Owner->save();
                    $countNews += 1;
                }

            }

            $this->info('Finaliza registro de co-propietarios actuales'."\n");
            $this->info('Cantidad co_pp actualizados: ' . $countUpdate . "\n");
            $this->info('Cantidad co_pp registrados: ' . $countNews . "\n");

            return Command::SUCCESS;

        } catch (\Throwable $th) {
            echo($th);
        }

        
    }
}
