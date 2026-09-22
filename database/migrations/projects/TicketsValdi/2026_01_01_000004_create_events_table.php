<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();
            $table->foreignId('parent_event_id')
                ->nullable()
                ->constrained('events')
                ->nullOnDelete();
            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->text('image_url')
                ->nullable();
            $table->string('category')
                ->nullable();
            $table->string('venue_name')
                ->nullable();
            $table->text('venue_address')
                ->nullable();
            $table->timestampTz('starts_at');
            $table->timestampTz('ends_at')
                ->nullable();
            $table->string('status', 20)
                ->default('draft');
            $table->integer('total_capacity')
                ->nullable();
            $table->boolean('is_free')
                ->default(false);
            $table->timestampTz('published_at')
                ->nullable();
            $table->timestamps();

            // Listado público: eventos publicados ordenados por fecha.
            $table->index(['status', 'starts_at']);
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
