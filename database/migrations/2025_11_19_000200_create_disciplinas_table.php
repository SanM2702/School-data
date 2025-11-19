<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disciplinas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id');
            $table->string('tipo_falta'); // leve, moderado, grave, muy_grave
            $table->text('descripcion');
            $table->date('fecha');
            $table->boolean('notificado_acudiente')->default(false);
            $table->boolean('notificado_estudiante')->default(false);
            $table->string('presentador_nombre')->nullable();
            $table->string('presentador_cargo')->nullable(); // Docente, Rector, Coordinador de convivencia
            $table->string('estado')->default('Registrado'); // Registrado, Revisado, Sancionado, Cerrado, En apelación
            $table->string('tipo_sancion')->nullable();
            $table->boolean('confirmacion_acudiente')->default(false);
            $table->text('respuesta_acudiente')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('estudiante_id')->references('idEstudiante')->on('estudiantes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinas');
    }
};
