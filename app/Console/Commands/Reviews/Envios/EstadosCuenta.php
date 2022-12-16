<?php

namespace App\Console\Commands\Reviews\Envios;

use App\Models\LogsEstadosCuenta;
use App\Models\Reviews\ReviewEstadosCuenta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EstadosCuenta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Review:EnvioEstadosCuenta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de revisar si todos los archivos fueron creados correctamente para cada propietario';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try 
        {
            $this->info('Inicia revisión de envio estados de cuenta propietarios');

            //Mes actual
            $month = date('m');

            //Fecha actual para created_at y updated_at
            date_default_timezone_set("America/Bogota");
        
            //Fecha actual fecha y hora junta
            $todayDateTime = date("Y-m-d H:i:s");

            ini_set('memory_limit', '-1');
            $prop_actuales = DB::table('bitacora__inm_arrendados_actuales')
                            ->select('Ced_Prop')
                            ->distinct()
                            ->get();

            //Contador de registros
            $count = 0;

            //Barra de progreso
            $progressBar = $this->output->createProgressBar(count($prop_actuales));

            //StarProgreso
            $progressBar->start();

            foreach ($prop_actuales as $prop_actual) 
            {
                //Contador de propietarios
                $count += 1;

                //Cédula propietario en propietarios actuales
                $ced_prop = $prop_actual->Ced_Prop;

                //Búsqueda en base de datos HFB - Logs
                $table_logs = LogsEstadosCuenta::where('id_destinatario', $ced_prop)
                            ->whereMonth('created_at', $month)
                            ->get();

                /** Validación, existe o no el registro en la tabla HFB de logs.
                * De la misma manera esta validando si el archivo fue creado, ya que si no existe id_destinatario es porque
                * el archivo no existió al momento de realizar el envio.
                */

                if( sizeof($table_logs) != 0 )
                {
                    //Recorrer resultados de búsqueda $table_logs, acceder a sus valores.
                    foreach ($table_logs as $table_log)
                    {
                        //Log encontrado, archivo creado y leído correctamente

                        //Registro en tabla de revisión
                        $table_review = new ReviewEstadosCuenta();
                        $table_review->archivo_es_creado = '1';
                        $table_review->es_enviado = $table_log->es_enviado;
                        $table_review->id_destinatario = $table_log->id_destinatario;
                        $table_review->id_log = $table_log->id;
                        $table_review->created_at = $todayDateTime;
                        $table_review->updated_at = $todayDateTime;
                        $table_review->save();
                
                    }

                }
                else
                {
                    //Log no encontrado, archivo no creado

                    //Registro en tabla de revisión
                    $table_review = new ReviewEstadosCuenta();
                    $table_review->archivo_es_creado = '0';
                    $table_review->es_enviado = '0';
                    $table_review->id_destinatario = $ced_prop;
                    $table_review->id_log = null;
                    $table_review->created_at = $todayDateTime;
                    $table_review->updated_at = $todayDateTime;
                    $table_review->save();

                }

                //Avance del progreso
                $progressBar->advance();
            }

            $progressBar->finish();

            $this->info("\n".'Propietarios activos: '.$count);
            $this->info('Finaliza revisión de envio estados de cuenta propietarios');

        }
        catch (\Throwable $th)
        {
            throw $th;
        }

        return Command::SUCCESS;
    }
}
