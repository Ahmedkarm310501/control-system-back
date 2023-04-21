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
        Schema::create('course_semester_enrollments', function (Blueprint $table) {
            // $table->id();
            // $table->float('course_grade');
            $table->foreignId('course_id')->references('id')->on('courses')->constrained();
            $table->foreignId('semester_id')->references('id')->on('semesters')->constrained();
            $table->foreignId('student_id')->references('id')->on('students')->constrained();
            $table->float('term_work');
            $table->float('exam_work');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_semester_enrollments');
    }
};
