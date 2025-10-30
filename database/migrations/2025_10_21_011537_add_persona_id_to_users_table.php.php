<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('persona_id')->nullable()->after('id');
        });

        // Solo si la tabla personas existe
        if (Schema::hasTable('personas')) {
            DB::statement("
                UPDATE users u
                INNER JOIN personas p ON u.email = p.email
                SET u.persona_id = p.idPersona
                WHERE p.email IS NOT NULL
            ");

            DB::statement("
                UPDATE users u
                INNER JOIN personas p ON u.persona_id = p.idPersona
                SET u.name = TRIM(CONCAT(p.primerNombre, ' ', COALESCE(p.segundoNombre, '')))
                WHERE p.primerNombre IS NOT NULL
            ");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('persona_id')
                ->references('idPersona')
                ->on('personas')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
    }
};
