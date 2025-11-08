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
        Schema::table('test_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('test_attempts', 'participant_name')) {
                $table->string('participant_name')->default('');
            }

            if (! Schema::hasColumn('test_attempts', 'participant_email')) {
                $table->string('participant_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('test_attempts', 'participant_email')) {
                $table->dropColumn('participant_email');
            }

            if (Schema::hasColumn('test_attempts', 'participant_name')) {
                $table->dropColumn('participant_name');
            }
        });
    }
};
