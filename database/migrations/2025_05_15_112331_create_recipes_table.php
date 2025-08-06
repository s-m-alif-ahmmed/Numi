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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipe_api_id')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('image')->nullable();
            $table->text('image_url')->nullable();
            $table->text('source_url')->nullable();
            $table->string('category')->nullable();
            $table->string('preparation_time')->nullable();
            $table->string('cooking_time')->nullable();
            $table->string('total_ready_time')->nullable();
            $table->string('servings')->nullable();
            $table->longText('description')->nullable();
            $table->longText('instruction')->nullable();
            $table->string('calories')->nullable();
            $table->string('protein')->nullable();
            $table->string('fat')->nullable();
            $table->string('carbs')->nullable();
            $table->enum('level', ['Easy', 'Medium', 'Hard'])->default("Easy");
            $table->enum('status',['Active', 'Draft', 'Inactive'])->default("Active");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
