<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviewer_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // interviewer
            $table->foreignId('taxpayer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('category', 50)->nullable();
            $table->text('body');
            $table->string('status', 50)->default('draft'); // draft, submitted, approved, rejected
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['user_id','taxpayer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviewer_reports');
    }
};
