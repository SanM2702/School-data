<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (! Schema::hasColumn('cursos', 'grado')) {
                $table->string('grado')->nullable()->after('nombre');
            }
            if (! Schema::hasColumn('cursos', 'grupo')) {
                $table->string('grupo')->nullable()->after('grado');
            }
        });

        // Backfill grado/grupo from existing nombre when possible (e.g. 'Primero A')
        $cursos = DB::table('cursos')->select('idCurso', 'nombre')->get();
        foreach ($cursos as $c) {
            if (empty($c->nombre)) {
                continue;
            }
            // Try to split by last space: grado = everything before last space, grupo = last token
            $parts = preg_split('/\s+/', trim($c->nombre));
            if (count($parts) >= 2) {
                $grupo = array_pop($parts);
                $grado = implode(' ', $parts);
            } else {
                $grado = $c->nombre;
                $grupo = null;
            }
            DB::table('cursos')->where('idCurso', $c->idCurso)->update([
                'grado' => $grado,
                'grupo' => $grupo,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (Schema::hasColumn('cursos', 'grupo')) {
                $table->dropColumn('grupo');
            }
            if (Schema::hasColumn('cursos', 'grado')) {
                $table->dropColumn('grado');
            }
        });
    }
};
