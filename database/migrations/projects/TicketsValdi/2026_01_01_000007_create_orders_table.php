<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->restrictOnDelete();
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained('coupons')
                ->nullOnDelete();
            $table->string('status', 20)
                ->default('pending');
            $table->bigInteger('subtotal_clp')
                ->default(0);
            $table->bigInteger('discount_clp')
                ->default(0);
            $table->bigInteger('total_clp')
                ->default(0);
            // El cupo ya está reservado al crear la orden. Este campo marca hasta
            // cuándo se sostiene esa reserva; la liberación automática todavía no
            // está implementada. Ver DOMAIN.md BR-12.
            $table->timestampTz('expires_at')
                ->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
