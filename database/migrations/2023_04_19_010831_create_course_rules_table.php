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
        Schema::create('course_rules', function (Blueprint $table) {
            $table->id();
            $table->float('term_work');
            $table->float('exam_work');
            $table->float('total');
            $table->float('exam_pass_mark');
            $table->string('instructor');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_rules');
    }
};