<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreguntasRecuperacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preguntas_recuperacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id');
            $table->string('pregunta');
            $table->string('respuesta');
            $table->string('contrasenia');
            $table->timestamps();

            // Definir la clave foránea
            $table->foreign('documento_id')->references('id')->on('documentos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preguntas_recuperacion');
    }
}
