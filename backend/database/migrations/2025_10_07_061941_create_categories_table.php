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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name', 100)->unique();
            $table->text('description',255)->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('slug', 100)->unique();
            $table->unsignedBigInteger('products_count')->default(0);
            $table->timestamps();
            
            $table->index('parent_id');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('products_count');
        });
    }
};
