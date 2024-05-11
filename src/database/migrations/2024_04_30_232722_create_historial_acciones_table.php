<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migración para la tabla HistorialAcciones
    public function up()
    {
        Schema::create('historial_acciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('UsuarioID');
            $table->unsignedBigInteger('DocumentoID');
            $table->string('TipoAccion');
            $table->text('Detalles')->nullable();
            $table->timestamp('FechaHora')->nullable();
            $table->timestamps();

            $table->foreign('UsuarioID')->references('ID')->on('usuarios');
            $table->foreign('DocumentoID')->references('ID')->on('documentos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_acciones');
    }
};
