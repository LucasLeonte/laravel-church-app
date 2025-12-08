<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('translation', 32)->default('NIV');
            $table->string('book');
            $table->unsignedSmallInteger('chapter');
            $table->unsignedSmallInteger('verse');
            $table->timestamps();

            $table->unique(['user_id', 'translation', 'book', 'chapter', 'verse'], 'fav_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_verses');
    }
};

