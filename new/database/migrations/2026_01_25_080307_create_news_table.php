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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            
            // Core Fields
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            
            // 👇 YEH RAHE NAYE COLUMNS (Sab ek saath)
            $table->text('excerpt')->nullable(); // Summary ke liye
            $table->json('image')->nullable();   // Multiple Images ke liye (Ab ye array store karega)
            
            $table->text('content'); // LongText bhi kar sakte ho agar content bahut bada hai
            
            $table->boolean('is_breaking')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            
            // User & Author Info
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('author_name')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};