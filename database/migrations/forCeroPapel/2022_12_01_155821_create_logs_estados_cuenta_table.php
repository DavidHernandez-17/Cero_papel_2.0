<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_estados_cuenta', function (Blueprint $table) {
            $table->id()->identity();
            $table->text('mensaje');
            $table->string('nombre_envio');
            $table->string('remitente');
            $table->string('asunto');
            $table->boolean('es_enviado');
            $table->string('nombre_adjunto');
            $table->string('fecha_envio');
            $table->string('hora_envio');
            $table->text('destinatario');
            $table->string('id_destinatario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs_estados_cuenta');
    }
};
