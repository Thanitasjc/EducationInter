<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('layout')->default('cards'); // pathways_split, cards, banner, cta
            $table->string('title_th')->nullable();
            $table->string('title_en')->nullable();
            $table->text('subtitle_th')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->string('cover_path')->nullable();
            $table->json('items')->nullable();
            $table->string('cta_label_th')->nullable();
            $table->string('cta_label_en')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
