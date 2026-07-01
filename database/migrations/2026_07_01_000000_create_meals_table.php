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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('emoji', 16);
            $table->string('category');
            $table->unsignedSmallInteger('time_minutes');
            $table->string('difficulty');
            $table->decimal('price', 6, 2);
            $table->string('color_start', 9);
            $table->string('color_end', 9);
            $table->string('accent_color', 9);
            $table->text('description');
            $table->json('ingredients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
