<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_th');
            $table->string('name_en');
            $table->string('code', 8)->nullable();
            $table->string('flag_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->text('summary_th')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('content_th')->nullable();
            $table->longText('content_en')->nullable();
            $table->json('tuition_info')->nullable();
            $table->json('living_cost_info')->nullable();
            $table->json('visa_info')->nullable();
            $table->json('intakes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name_th');
            $table->string('name_en');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['country_id', 'slug']);
        });

        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name_th');
            $table->string('name_en');
            $table->string('type')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('ranking_qs')->nullable();
            $table->unsignedInteger('ranking_the')->nullable();
            $table->decimal('tuition_min', 12, 2)->nullable();
            $table->decimal('tuition_max', 12, 2)->nullable();
            $table->string('currency', 8)->default('GBP');
            $table->text('about_th')->nullable();
            $table->text('about_en')->nullable();
            $table->json('entry_requirements')->nullable();
            $table->json('accommodation_info')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_th');
            $table->string('name_en');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug');
            $table->string('name_th');
            $table->string('name_en');
            $table->string('degree_level')->nullable();
            $table->unsignedInteger('duration_months')->nullable();
            $table->decimal('tuition', 12, 2)->nullable();
            $table->string('currency', 8)->default('GBP');
            $table->json('intakes')->nullable();
            $table->json('entry_requirements')->nullable();
            $table->json('english_requirements')->nullable();
            $table->text('summary_th')->nullable();
            $table->text('summary_en')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['university_id', 'slug']);
        });

        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('university_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title_th');
            $table->string('title_en');
            $table->string('amount_label_th')->nullable();
            $table->string('amount_label_en')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('logo_path')->nullable();
            $table->date('deadline')->nullable();
            $table->json('eligibility')->nullable();
            $table->json('requirements')->nullable();
            $table->text('how_to_apply_th')->nullable();
            $table->text('how_to_apply_en')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_th');
            $table->string('title_en');
            $table->text('summary_th')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('content_th')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('image_path')->nullable();
            $table->string('cta_label_th')->nullable();
            $table->string('cta_label_en')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_th');
            $table->string('name_en');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title_th');
            $table->string('title_en');
            $table->text('excerpt_th')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_th')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('cover_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('university_label')->nullable();
            $table->string('country_label')->nullable();
            $table->string('year', 10)->nullable();
            $table->text('quote_th');
            $table->text('quote_en');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('locale', 5)->default('th');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('keywords')->nullable();
            $table->json('schema_json')->nullable();
            $table->string('robots')->nullable();
            $table->timestamps();
            $table->unique(['seoable_type', 'seoable_id', 'locale']);
        });

        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_th');
            $table->string('title_en');
            $table->text('summary_th')->nullable();
            $table->text('summary_en')->nullable();
            $table->string('cover_path')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('page_contents');
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('scholarships');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
};
