<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();
            $table->string('code', 50);
            $table->text('description')
                ->nullable();
            // Porcentaje o monto fijo, no ambos. Ver DOMAIN.md BR-11.
            $table->decimal('discount_percent', 5, 2)
                ->nullable();
            $table->integer('discount_clp')
                ->nullable();
            $table->integer('max_uses')
                ->nullable();
            $table->integer('uses_count')
                ->default(0);
            $table->timestampTz('valid_from')
                ->nullable();
            $table->timestampTz('valid_until')
                ->nullable();
            $table->boolean('enabled')
                ->default(true);
            $table->timestamps();

            // El código es único por institución, no globalmente.
            $table->unique(['institution_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
