<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->id();

            $table->string('email')->unique();

            // MySQL cannot reliably enforce UNIQUE on LONGTEXT, so we store a hash for uniqueness.
            $table->longText('description');
            $table->char('description_hash', 64)->unique();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('address', 512);

            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->string('gender', 32)->nullable();
            $table->unsignedSmallInteger('age')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
};

