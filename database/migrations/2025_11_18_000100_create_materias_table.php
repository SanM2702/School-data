<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id('idMateria');
            $table->string('nombre');
            $table->unsignedBigInteger('curso_id');
            $table->unsignedBigInteger('docente_id')->nullable();
            $table->string('codigo')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('curso_id')->references('idCurso')->on('cursos')->onDelete('cascade');
            $table->foreign('docente_id')->references('idDocente')->on('docentes')->nullOnDelete();
            $table->index(['curso_id', 'docente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
