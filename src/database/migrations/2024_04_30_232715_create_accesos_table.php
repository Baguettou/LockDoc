<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migración para la tabla Accesos
    public function up()
    {
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('UsuarioID');
            $table->timestamp('FechaAcceso')->nullable();
            $table->timestamps();

            $table->foreign('UsuarioID')->references('ID')->on('usuarios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accesos');
    }
};
