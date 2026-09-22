<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alcance de visibilidad de los usuarios del panel.
 *
 * institution_id NULL  -> usuario global (admin del landlord / super_admin): ve todas
 *                         las instituciones del tenant.
 * institution_id SET   -> usuario de esa institución: solo ve lo suyo.
 *
 * Extiende la tabla `users` que crean las migraciones Common. Ver
 * app/Projects/TicketsValdi/docs/decisions/0005-institution-scope-on-platform-users.md
 */
return new class() extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institution_id')
                ->nullable()
                ->after('is_system_user')
                ->constrained('institutions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }
};
