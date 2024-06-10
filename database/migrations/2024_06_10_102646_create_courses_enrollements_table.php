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
        Schema::create('courses_enrollements', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');
            $table->string('course_id');
            $table->dateTime('enrollment_date');
            $table->string('enrollment_no')->unique();
            $table->string('grade')->nullable();
            $table->string('payment_type');
            $table->string('createdBy')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses_enrollements');
    }
};
