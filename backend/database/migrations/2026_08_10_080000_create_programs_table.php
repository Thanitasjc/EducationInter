<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_th');
            $table->string('title_en');
            $table->text('summary_th')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('content_th')->nullable();
            $table->longText('content_en')->nullable();
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->string('duration_label_th')->nullable();
            $table->string('duration_label_en')->nullable();
            $table->string('language')->nullable(); // english, japanese, etc.
            $table->json('destinations')->nullable(); // ["uk","usa",...]
            $table->string('cover_path')->nullable();
            $table->string('cta_label_th')->nullable();
            $table->string('cta_label_en')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
