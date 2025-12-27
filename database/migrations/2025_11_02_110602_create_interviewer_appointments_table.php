<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviewer_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('taxpayer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('interviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->enum('status', ['scheduled','completed'])->default('scheduled');
            $table->string('location')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
            $table->index(['user_id','start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviewer_appointments');
    }
};
