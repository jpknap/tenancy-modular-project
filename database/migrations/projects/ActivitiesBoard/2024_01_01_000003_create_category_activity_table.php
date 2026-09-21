<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_activity', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('activity_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamp('created_at')
                ->nullable();

            $table->primary(['category_id', 'activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_activity');
    }
};
