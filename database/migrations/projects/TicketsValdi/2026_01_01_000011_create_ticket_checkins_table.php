<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('ticket_checkins', function (Blueprint $table) {
            $table->id();
            // Único: un ticket se valida una sola vez. El segundo escaneo se rechaza
            // a nivel de base de datos, no solo de aplicación. BR-15.
            $table->foreignId('ticket_id')
                ->unique()
                ->constrained('tickets')
                ->cascadeOnDelete();
            // Usuario del panel que validó (rol validator). NULL si se validó por un
            // proceso automático o si el usuario fue dado de baja.
            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestampTz('validated_at')
                ->useCurrent();
            $table->text('device_info')
                ->nullable();
            $table->string('ip_address', 45)
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checkins');
    }
};
