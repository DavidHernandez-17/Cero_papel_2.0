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
        Schema::create('owner__coowners', function (Blueprint $table) {
            $table->id();
            $table->string('id_inmueble');
            $table->string('ced_cliente');
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('tipo_cliente');
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
        Schema::dropIfExists('owner__coowners');
    }
};
