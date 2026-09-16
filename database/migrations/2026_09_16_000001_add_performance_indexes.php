<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'idx_users_role_status');
            $table->index('status', 'idx_users_status');
        });

        Schema::table('collection_tasks', function (Blueprint $table) {
            $table->index(['collection_date', 'status'], 'idx_tasks_date_status');
            $table->index('status', 'idx_tasks_status');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_reports_status_created');
            $table->index(['type', 'status'], 'idx_reports_type_status');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_redemptions_status_created');
        });

        Schema::table('points', function (Blueprint $table) {
            $table->index(['collection_task_id', 'resident_id'], 'idx_points_task_resident');
        });
    }

    public function down(): void
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropIndex('idx_points_task_resident');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex('idx_redemptions_status_created');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_status_created');
            $table->dropIndex('idx_reports_type_status');
        });

        Schema::table('collection_tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_date_status');
            $table->dropIndex('idx_tasks_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_status');
            $table->dropIndex('idx_users_status');
        });
    }
};
