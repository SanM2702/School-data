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
        Schema::create('estudiante_acudiente', function (Blueprint $table) {
            $table->unsignedBigInteger('idEstudiante');
            $table->unsignedBigInteger('idAcudiente');
            $table->primary(['idEstudiante', 'idAcudiente']);
            
            $table->foreign('idEstudiante')
                ->references('idEstudiante')
                ->on('estudiantes')
                ->onDelete('cascade');
                
            $table->foreign('idAcudiente')
                ->references('idAcudiente')
                ->on('acudientes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante_acudiente');
    }
};
