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
        Schema::create('member_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();

            // Snapshot fields (captured at purchase time)
            $table->string('snapshot_membership_name');
            $table->integer('snapshot_max_attendance_qty')->nullable(); // NULL = unlimited
            $table->integer('snapshot_duration_days');
            $table->decimal('snapshot_price', 12, 2);

            // Status & dates
            $table->timestamp('started_at');
            $table->timestamp('expired_at');
            $table->string('status')->default('active'); // active, expired, cancelled

            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['expired_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_memberships');
    }
};
