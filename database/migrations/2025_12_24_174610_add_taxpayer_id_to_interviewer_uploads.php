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
        Schema::table('interviewer_uploads', function (Blueprint $table) {
            $table->foreignId('taxpayer_id')
                ->after('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interviewer_uploads', function (Blueprint $table) {
            //
        });
    }
};
