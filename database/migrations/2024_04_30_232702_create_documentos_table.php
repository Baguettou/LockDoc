<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migración para la tabla Documentos
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('UsuarioID');
            $table->string('NombreArchivo');
            $table->timestamp('FechaCarga')->nullable();
            $table->string('RutaArchivo');
            $table->timestamps();

            $table->foreign('UsuarioID')->references('ID')->on('usuarios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentos');
    }
};
