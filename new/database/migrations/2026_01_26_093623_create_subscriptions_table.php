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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // 1. SUBSCRIBER (Jo button daba raha hai)
            // 'constrained' ka matlab ye 'users' table ki id honi chahiye
            $table->foreignId('subscriber_id')->constrained('users')->onDelete('cascade'); 

            // 2. AUTHOR (Jisko subscribe kiya ja raha hai)
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade'); 

            // 3. TIME (Kab subscribe kiya)
            $table->timestamps();

            // 4. RULE: Ek banda ek hi author ko do baar subscribe na kar sake
            $table->unique(['subscriber_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};