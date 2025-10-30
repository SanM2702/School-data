<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            if (!Schema::hasColumn('personas', 'primerApellido')) {
                $table->string('primerApellido')->after('segundoNombre');
            }
            if (!Schema::hasColumn('personas', 'segundoApellido')) {
                $table->string('segundoApellido')->nullable()->after('primerApellido');
            }
            if (!Schema::hasColumn('personas', 'telefono')) {
                $table->string('telefono')->nullable()->after('segundoApellido');
            }
            if (!Schema::hasColumn('personas', 'noDocumento')) {
                $table->string('noDocumento')->unique()->after('telefono');
            }
            if (!Schema::hasColumn('personas', 'fechaNacimiento')) {
                $table->date('fechaNacimiento')->nullable()->after('noDocumento');
            }
            if (!Schema::hasColumn('personas', 'estado')) {
                $table->boolean('estado')->default(true)->after('fechaNacimiento');
            }
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            if (Schema::hasColumn('personas', 'estado')) {
                $table->dropColumn('estado');
            }
            if (Schema::hasColumn('personas', 'fechaNacimiento')) {
                $table->dropColumn('fechaNacimiento');
            }
            if (Schema::hasColumn('personas', 'noDocumento')) {
                // Drop unique index first if exists (name is convention-based)
                try { DB::statement('ALTER TABLE personas DROP INDEX personas_noDocumento_unique'); } catch (\Throwable $e) {}
                $table->dropColumn('noDocumento');
            }
            if (Schema::hasColumn('personas', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('personas', 'segundoApellido')) {
                $table->dropColumn('segundoApellido');
            }
            if (Schema::hasColumn('personas', 'primerApellido')) {
                $table->dropColumn('primerApellido');
            }
        });
    }
};
