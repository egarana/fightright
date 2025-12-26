<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds audit trail fields to track which admin/staff recorded the attendance.
     * Fields are nullable for backward compatibility with existing records.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Foreign key to users table - who recorded this attendance
            // Nullable for existing historical records
            // Set to null if user is deleted (preserving the snapshot name)
            $table->foreignId('recorded_by_user_id')
                ->nullable()
                ->after('notes')
                ->constrained('users')
                ->nullOnDelete();

            // Snapshot of admin name at time of recording
            // Preserved even if user changes name or is deleted
            $table->string('snapshot_recorded_by_name')
                ->nullable()
                ->after('recorded_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['recorded_by_user_id']);
            $table->dropColumn(['recorded_by_user_id', 'snapshot_recorded_by_name']);
        });
    }
};
