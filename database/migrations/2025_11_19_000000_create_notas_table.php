<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id('idNota');
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('materia_id');
            $table->decimal('valor', 5, 2)->nullable();
            $table->string('periodo', 50)->nullable();
            $table->timestamps();

            $table->foreign('estudiante_id')->references('idEstudiante')->on('estudiantes')->onDelete('cascade');
            $table->foreign('materia_id')->references('idMateria')->on('materias')->onDelete('cascade');
            $table->unique(['estudiante_id','materia_id','periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
