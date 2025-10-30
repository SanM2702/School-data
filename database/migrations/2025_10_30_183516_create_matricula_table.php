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
        Schema::create('matricula', function (Blueprint $table) {
            $table->id('idMatricula');
            $table->unsignedBigInteger('idEstudiante');
            $table->enum('estado', ['activo', 'inactivo', 'en_proceso'])->default('en_proceso');
            $table->date('fechaMatricula')->nullable();
            // Opcional: curso_id, año, etc.
            $table->timestamps();

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
        Schema::dropIfExists('matricula');
    }
};
