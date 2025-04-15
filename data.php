<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Classes Table
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('level');
            $table->timestamps();
        });

        // Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->foreignId('class_id')->constrained()->onDelete('restrict');
            $table->timestamps();
        });

        // Parents Table
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20);
            $table->timestamps();
        });

        // Parent-Student Pivot Table
        Schema::create('parent_student', function (Blueprint $table) {
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->primary(['parent_id', 'student_id']);
        });

        // Subjects Table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('class_id')->constrained()->onDelete('restrict');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        // Absences Table
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->date('date');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // Grades Table
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->decimal('grade', 5, 2);
            $table->date('exam_date');
            $table->timestamps();
        });

        // Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_type', ['tuition', 'transport', 'other']);
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->timestamps();
        });

        // Transports Table
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number', 50);
            $table->string('driver_name');
            $table->text('route_description');
            $table->timestamps();
        });

        // Student-Transport Pivot Table
        Schema::create('student_transport', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_id')->constrained()->onDelete('restrict');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->primary(['student_id', 'transport_id']);
        });

        // Timetables Table
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('restrict');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // Accountants Table
        Schema::create('accountants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accountants');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('student_transport');
        Schema::dropIfExists('transports');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('absences');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('users');
    }
};
