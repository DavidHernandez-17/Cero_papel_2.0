<?php

namespace App\Console\Commands\Migraciones;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UploadMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Migraciones:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de realizar las migraciones a las diferentes bases de datos relacionadas en este sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try 
        {
            //Migraciones BD cero papel
            Artisan::call('migrate', [
                '--path' => 'database/migrations/forCeroPapel',
                '--database' => 'mysql',
                '--force' => true
            ]);
            
            echo('Migraciones realizadas correctamente.');
        } 
        catch (\Throwable $th)
        {
            throw $th;
        }

        return Command::SUCCESS;
    }
}
