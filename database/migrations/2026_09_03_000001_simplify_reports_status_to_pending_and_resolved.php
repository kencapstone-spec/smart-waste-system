<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Automatically convert any existing 'reviewed' reports to 'resolved'
        try {
            DB::table('reports')->where('status', 'reviewed')->update([
                'status' => 'resolved',
            ]);
        } catch (\Throwable $e) {
            // Ignore if DB connection is unavailable during build
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed
    }
};
