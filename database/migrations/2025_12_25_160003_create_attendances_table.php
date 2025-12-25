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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_membership_id')->constrained()->cascadeOnDelete();

            // Snapshot fields (captured at check-in time)
            $table->string('snapshot_member_name');
            $table->string('snapshot_membership_name');
            $table->integer('snapshot_remaining_before')->nullable(); // NULL = unlimited

            // Check-in/out times
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['member_membership_id', 'check_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
