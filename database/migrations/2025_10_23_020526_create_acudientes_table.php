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

        Schema::create('acudientes', function (Blueprint $table) {
            $table->id('idAcudiente');
            $table->unsignedBigInteger('idPersona'); // referencia a la persona (nombre, email, etc.)
            $table->string('parentesco'); // padre, madre, tutor, etc.
            $table->timestamps();

            $table->foreign('idPersona')
                ->references('idPersona')
                ->on('personas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acudientes');
    }
};
