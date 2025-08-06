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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category_id')->nullable();
            $table->string('ingredient_api_id')->nullable();
            $table->string('name');
            $table->string('aisel')->nullable();
            $table->string('consistency')->nullable();
            $table->string('original_name')->nullable();
            $table->json('meta')->nullable();
            $table->json('measures')->nullable();
            $table->string('amount')->nullable();
            $table->string('unit')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
