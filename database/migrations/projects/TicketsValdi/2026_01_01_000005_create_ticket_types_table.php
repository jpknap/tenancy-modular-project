<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->string('seating_type', 20)
                ->default('general');
            $table->integer('price_clp')
                ->default(0);
            $table->integer('capacity')
                ->nullable();
            // Se incrementa al confirmar la orden: el cupo se reserva al comprar,
            // no al pagar. Ver DOMAIN.md BR-12.
            $table->integer('sold_count')
                ->default(0);
            $table->integer('max_per_order')
                ->nullable();
            $table->timestampTz('sales_start_at')
                ->nullable();
            $table->timestampTz('sales_end_at')
                ->nullable();
            $table->boolean('enabled')
                ->default(true);
            $table->timestamps();

            $table->index(['event_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
