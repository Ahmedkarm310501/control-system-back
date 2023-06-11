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
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->constrained();
            // $table->foreignId('course_id')->references('id')->on('courses')->constrained();
            $table->foreignId('course_semester_id')->references('id')->on('course_semesters')->constrained();
            $table->foreignId('course_id')->references('course_id')->on('course_semesters')->constrained();
            $table->foreignId('semester_id')->references('semester_id')->on('course_semesters')->constrained();
            $table->unique(['user_id', 'course_id', 'semester_id'], 'course_user_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_users');
    }
};
