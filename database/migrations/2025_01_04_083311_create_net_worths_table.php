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
            $table->string('jan', 15)->default(0); 
            $table->string('feb', 15)->default(0); 
            $table->string('mar', 15)->default(0); 
            $table->string('apr', 15)->default(0); 
            $table->string('may', 15)->default(0); 
            $table->string('jun', 15)->default(0); 
            $table->string('jul', 15)->default(0); 
            $table->string('aug', 15)->default(0); 
            $table->string('sep', 15)->default(0); 
            $table->string('oct', 15)->default(0); 
            $table->string('nov', 15)->default(0); 
            $table->string('dec', 15)->default(0); 
            $table->string('net_worth', 20)->default(0); 
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

