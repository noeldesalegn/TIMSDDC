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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('tin_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('tin');

            $table->timestamp('tin_verified_at')->nullable()->after('tin_status');
            $table->foreignId('tin_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('tin_verified_at');

            $table->text('tin_rejection_reason')->nullable()->after('tin_verified_by');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
