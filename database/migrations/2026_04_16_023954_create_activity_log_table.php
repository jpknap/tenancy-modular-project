<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamps();

            // Índices de performance
            $table->index('log_name');
            $table->index(['subject_type', 'subject_id', 'created_at'], 'al_subject_created');
            $table->index(['causer_type', 'causer_id', 'created_at'], 'al_causer_created');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
}
