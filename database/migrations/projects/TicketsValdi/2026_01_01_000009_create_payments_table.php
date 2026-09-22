<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->string('provider', 50);
            $table->string('provider_session_id')
                ->nullable();
            $table->string('provider_payment_id')
                ->nullable();
            $table->string('buy_order', 100);
            $table->bigInteger('amount_clp');
            $table->string('status', 20)
                ->default('initiated');
            $table->text('status_detail')
                ->nullable();
            $table->string('response_code', 50)
                ->nullable();
            $table->string('card_last4', 4)
                ->nullable();
            $table->string('card_brand', 50)
                ->nullable();
            $table->string('payment_method', 50)
                ->nullable();
            $table->timestampTz('initiated_at')
                ->useCurrent();
            $table->timestampTz('authorized_at')
                ->nullable();
            $table->timestampTz('paid_at')
                ->nullable();
            $table->timestampTz('failed_at')
                ->nullable();
            // Payloads crudos de la pasarela, para auditoría y conciliación.
            // jsonb (no json): permite indexar e interrogar el contenido en Postgres.
            $table->jsonb('init_response')
                ->nullable();
            $table->jsonb('commit_response')
                ->nullable();
            $table->jsonb('webhook_response')
                ->nullable();
            $table->timestamps();

            // Idempotencia: reprocesar el mismo webhook no puede duplicar efectos.
            $table->unique(['provider', 'buy_order']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
