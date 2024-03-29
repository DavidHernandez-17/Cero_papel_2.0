<?php

namespace App\Console;

use App\Console\Commands\SendEmailProprietary;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
     /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        Commands\SendEmailProprietary::class
    ];


    protected function schedule(Schedule $schedule)
    {
        // para producción
        // $schedule->command('SendEmail:EstadosCuenta')->timezone('America/Bogota')->dailyAt('03:00');
        // $schedule->command('Review:EnvioEstadosCuenta')->timezone('America/Bogota')->monthlyOn(28, '23:00');

        // para pruebas
        $schedule->command('SendEmail:EstadosCuenta')->everyTenMinutes();
        $schedule->command('Review:EnvioEstadosCuenta')->everyTenMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
