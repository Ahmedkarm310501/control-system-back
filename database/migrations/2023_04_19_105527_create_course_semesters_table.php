<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->references('id')->on('courses')->constrained();
            $table->foreignId('semester_id')->references('id')->on('semesters')->constrained();
            $table->string('stud_names')->nullable();
            $table->string('stud_grades')->nullable();
            $table->string('stud_term_work')->nullable();
            $table->string('stud_exam_work')->nullable();
            $table->string('stud_extra_grade')->nullable();

            $table->unique(['course_id', 'semester_id'], 'course_semester_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_semesters');
    }
};