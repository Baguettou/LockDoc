<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migración para la tabla UsuariosRoles
    public function up()
    {
        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('UsuarioID');
            $table->unsignedBigInteger('RolID');
            $table->timestamps();

            $table->foreign('UsuarioID')->references('ID')->on('usuarios');
            $table->foreign('RolID')->references('ID')->on('roles');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios_roles');
    }
};
