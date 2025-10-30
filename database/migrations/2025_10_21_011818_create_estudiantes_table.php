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
    Schema::create('estudiantes', function (Blueprint $table) {
            // Primary key named idEstudiante
            $table->id('idEstudiante');

            // Logical inheritance: reference to personas
            $table->unsignedBigInteger('idPersona');

            // Student-specific
            $table->date('fechaIngreso')->nullable();
            //$table->unsignedBigInteger('curso_id')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('idPersona')->references('idPersona')->on('personas')->onDelete('cascade');
            // $table->foreign('curso_id')->references('idCurso')->on('cursos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('estudiantes');
    }
};
