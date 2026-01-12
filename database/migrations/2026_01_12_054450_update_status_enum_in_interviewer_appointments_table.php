<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL/MariaDB, we can use DB::statement to modify the enum column
        DB::statement("ALTER TABLE interviewer_appointments MODIFY COLUMN status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting might be dangerous if 'cancelled' values exist, but for completeness:
        // We would ideally map 'cancelled' back to something else or just leave it.
        // For now, we will just leave it as is or revert to original if needed.
        // DB::statement("ALTER TABLE interviewer_appointments MODIFY COLUMN status ENUM('scheduled', 'completed') DEFAULT 'scheduled'");
    }
};
