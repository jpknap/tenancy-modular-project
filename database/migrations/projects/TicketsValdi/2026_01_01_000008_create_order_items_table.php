<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->restrictOnDelete();
            $table->integer('quantity');
            // Precio congelado al momento de la compra: cambiar el precio del tipo
            // de entrada no altera órdenes ya creadas.
            $table->integer('unit_price_clp');
            $table->integer('subtotal_clp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
