<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scan_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('puesto')->nullable();
            $table->string('empresa')->nullable();
            $table->string('estado')->nullable();
            $table->string('telefono')->nullable();
            $table->string('rol')->nullable();
            $table->string('correo')->nullable()->index();
            $table->json('campos_adicionales')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};
