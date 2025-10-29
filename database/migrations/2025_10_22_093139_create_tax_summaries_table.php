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
        Schema::create('tax_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxpayer_id')->constrained('users')->onDelete('cascade');
            $table->enum('tax_type', [
                'Employment',
                'Business',
                'Rental',
            ]);
            $table->enum('category', ['A', 'B', 'C'])->nullable();
            $table->decimal('taxable_income', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('deductible', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->string('tax_period')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_summaries');
    }
};
