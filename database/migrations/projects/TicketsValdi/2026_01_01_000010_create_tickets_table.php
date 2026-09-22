<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->cascadeOnDelete();
            $table->foreignId('event_id')
                ->constrained('events')
                ->restrictOnDelete();
            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->restrictOnDelete();
            // Identificador público impreso en el QR.
            $table->string('qr_code', 100)
                ->unique();
            // Firma privada: nunca se expone en APIs ni en el QR visible. BR-14.
            $table->string('qr_secret', 100);
            $table->string('seat_row', 20)
                ->nullable();
            $table->string('seat_number', 20)
                ->nullable();
            // Titular: puede diferir del comprador (entrada a nombre de otra persona).
            $table->foreignId('holder_customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();
            $table->string('holder_name')
                ->nullable();
            $table->string('holder_document')
                ->nullable();
            $table->string('status', 20)
                ->default('reserved');
            $table->timestampTz('issued_at')
                ->nullable();
            $table->timestampTz('used_at')
                ->nullable();
            $table->timestamps();

            // Un asiento numerado no se vende dos veces. En PostgreSQL los NULL no
            // colisionan, así que las entradas generales (sin fila ni número) quedan
            // fuera de la restricción, que es el comportamiento buscado. BR-17.
            $table->unique(['event_id', 'ticket_type_id', 'seat_row', 'seat_number']);
            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
