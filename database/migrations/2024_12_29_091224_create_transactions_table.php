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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->foreignId('budget_id')->constrained()->onDelete('cascade'); // Link to budgets table
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Link to categories table
            $table->decimal('amount', 10, 2); // Store monetary value with two decimal places
            $table->string('description')->nullable(); // Optional description of the transaction
            $table->enum('type', ['income', 'expense']); // Type of transaction: income or expense
            $table->date('date'); // Date of the transaction
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
