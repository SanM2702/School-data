<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acudientes', function (Blueprint $table) {
            // PK personalizada
            $table->id('idAcudiente');

            // FKs según convenciones existentes
            $table->unsignedBigInteger('idPersona');
            $table->unsignedBigInteger('idEstudiante');

            // Datos propios del acudiente
            $table->string('parentesco');

            $table->timestamps();

            // Restricciones
            $table->foreign('idPersona')
                ->references('idPersona')
                ->on('personas')
                ->onDelete('cascade');

            $table->foreign('idEstudiante')
                ->references('idEstudiante')
                ->on('estudiantes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acudientes');
    }
};
