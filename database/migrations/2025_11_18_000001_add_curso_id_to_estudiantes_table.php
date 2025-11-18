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
        Schema::table('estudiantes', function (Blueprint $table) {
            // Añadir columna curso_id si no existe
            if (! Schema::hasColumn('estudiantes', 'curso_id')) {
                $table->unsignedBigInteger('curso_id')->nullable()->after('fechaIngreso');
                $table->foreign('curso_id')->references('idCurso')->on('cursos')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            if (Schema::hasColumn('estudiantes', 'curso_id')) {
                $table->dropForeign(['curso_id']);
                $table->dropColumn('curso_id');
            }
        });
    }
};
