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
        Schema::create('net_worths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Assets and Liability types
            $table->string('name'); // Name of the asset or liability
            $table->string('institution')->nullable(); // Optional
            $table->string('notes')->nullable(); // Optional notes
            $table->year('year'); // Year of the net worth
            $table->decimal('jan', 15, 2)->nullable(); // Optional
            $table->decimal('feb', 15, 2)->nullable(); // Optional
            $table->decimal('mar', 15, 2)->nullable(); // Optional
            $table->decimal('apr', 15, 2)->nullable(); // Optional
            $table->decimal('may', 15, 2)->nullable(); // Optional
            $table->decimal('jun', 15, 2)->nullable(); // Optional
            $table->decimal('jul', 15, 2)->nullable(); // Optional
            $table->decimal('aug', 15, 2)->nullable(); // Optional
            $table->decimal('sep', 15, 2)->nullable(); // Optional
            $table->decimal('oct', 15, 2)->nullable(); // Optional
            $table->decimal('nov', 15, 2)->nullable(); // Optional
            $table->decimal('dec', 15, 2)->nullable(); // Optional
            $table->decimal('net_worth', 20, 2)->nullable(); // Optional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('net_worths');
    }
};

