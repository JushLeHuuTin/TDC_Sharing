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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title', 130);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active','new','draft','pending','sold','hidden'])->default('active');
            $table->integer('stocks')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('slug', 130)->unique();
            $table->bigInteger('views_count')->default(0);
            $table->timestamps();
            // $table->softDeletes();
            
            $table->index('user_id');
            $table->index('category_id');
            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
