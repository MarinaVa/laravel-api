<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->integer('rickandmorty_id')->unsigned()->unique();
            $table->string('name', 100);
            $table->string('status', 100);
            $table->string('species', 100);
            $table->string('type', 20)->default('');
            $table->string('gender', 20);
            $table->string('origin_name', 100);
            $table->string('origin_url', 100);
            $table->string('location_name', 100);
            $table->string('location_url', 100);
            $table->string('image', 100);
            $table->string('url', 100);
            $table->timestamps();
            $table->index(['rickandmorty_id']);
            $table->index(['name']);
            $table->index(['status']);
            $table->index(['species']);
            $table->index(['gender']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
