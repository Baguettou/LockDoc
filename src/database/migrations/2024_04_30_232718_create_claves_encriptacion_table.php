<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migración para la tabla ClavesEncriptacion
    public function up()
    {
        Schema::create('claves_encriptacion', function (Blueprint $table) {
            $table->unsignedBigInteger('UsuarioID');
            $table->string('ClaveEncriptacion');
            $table->timestamps();

            $table->foreign('UsuarioID')->references('ID')->on('usuarios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('claves_encriptacion');
    }
};
