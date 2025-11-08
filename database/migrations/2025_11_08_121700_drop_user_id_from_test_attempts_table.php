<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('test_attempts', 'user_id')) {
            return;
        }

        Schema::rename('test_attempts', 'test_attempts_old');

        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->string('participant_name')->default('');
            $table->string('participant_email')->nullable();
            $table->string('status', 24)->default('in_progress');
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_questions')->default(0);
            $table->decimal('score_percent', 5, 2)->nullable();
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->timestamps();
        });

        DB::table('test_attempts_old')
            ->orderBy('id')
            ->each(function ($attempt) {
                DB::table('test_attempts')->insert([
                    'id' => $attempt->id,
                    'test_id' => $attempt->test_id,
                    'participant_name' => $attempt->participant_name ?? '',
                    'participant_email' => $attempt->participant_email,
                    'status' => $attempt->status,
                    'total_questions' => $attempt->total_questions,
                    'correct_questions' => $attempt->correct_questions,
                    'score_percent' => $attempt->score_percent,
                    'passed' => $attempt->passed,
                    'started_at' => $attempt->started_at,
                    'completed_at' => $attempt->completed_at,
                    'time_spent_seconds' => $attempt->time_spent_seconds,
                    'created_at' => $attempt->created_at,
                    'updated_at' => $attempt->updated_at,
                ]);
            });

        Schema::drop('test_attempts_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('test_attempts', 'test_attempts_new');

        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('participant_name')->default('');
            $table->string('participant_email')->nullable();
            $table->string('status', 24)->default('in_progress');
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_questions')->default(0);
            $table->decimal('score_percent', 5, 2)->nullable();
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->timestamps();
        });

        DB::table('test_attempts_new')
            ->orderBy('id')
            ->each(function ($attempt) {
                DB::table('test_attempts')->insert([
                    'id' => $attempt->id,
                    'test_id' => $attempt->test_id,
                    'user_id' => null,
                    'participant_name' => $attempt->participant_name ?? '',
                    'participant_email' => $attempt->participant_email,
                    'status' => $attempt->status,
                    'total_questions' => $attempt->total_questions,
                    'correct_questions' => $attempt->correct_questions,
                    'score_percent' => $attempt->score_percent,
                    'passed' => $attempt->passed,
                    'started_at' => $attempt->started_at,
                    'completed_at' => $attempt->completed_at,
                    'time_spent_seconds' => $attempt->time_spent_seconds,
                    'created_at' => $attempt->created_at,
                    'updated_at' => $attempt->updated_at,
                ]);
            });

        Schema::drop('test_attempts_new');
    }
};


