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
        // Tabla de competiciones deportivas
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')
                ->nullable();
            $table->string('sport_type'); // football, basketball, etc.
            $table->date('start_date');
            $table->date('end_date')
                ->nullable();
            $table->string('status')
                ->default('pending'); // pending, active, finished
            $table->timestamps();
        });

        // Tabla de equipos
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')
                ->unique();
            $table->text('description')
                ->nullable();
            $table->string('logo_url')
                ->nullable();
            $table->timestamps();
        });

        // Tabla de jugadores
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')
                ->nullable();
            $table->string('position')
                ->nullable();
            $table->integer('jersey_number')
                ->nullable();
            $table->timestamps();
        });

        // Tabla de partidos
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('home_team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->foreignId('away_team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->dateTime('match_date');
            $table->string('venue')
                ->nullable();
            $table->integer('home_score')
                ->nullable();
            $table->integer('away_score')
                ->nullable();
            $table->string('status')
                ->default('scheduled'); // scheduled, in_progress, finished, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('players');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('competitions');
    }
};
