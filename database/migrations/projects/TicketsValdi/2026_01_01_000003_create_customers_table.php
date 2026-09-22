<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Compradores de entradas. No son usuarios del panel: viven aparte de `users`.
 *
 * Soportan checkout como invitado — se crea el registro con el email de contacto y
 * `password` / `registered_at` quedan nulos. El registro formal es posterior y solo
 * agrega la contraseña sobre el mismo email, preservando el histórico de compras.
 */
return new class() extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('email')
                ->unique();
            $table->string('password')
                ->nullable();
            $table->string('name')
                ->nullable();
            $table->string('document_number')
                ->nullable();
            $table->string('phone')
                ->nullable();
            $table->boolean('enabled')
                ->default(true);
            $table->timestampTz('registered_at')
                ->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
