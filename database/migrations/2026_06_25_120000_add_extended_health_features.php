<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale', 5)->default('tr')->after('email');
        });

        Schema::table('blood_sugar_readings', function (Blueprint $table) {
            $table->string('mood', 30)->nullable()->after('notes');
            $table->decimal('sleep_hours', 3, 1)->nullable()->after('mood');
            $table->unsignedTinyInteger('stress_level')->nullable()->after('sleep_hours');
        });

        Schema::create('insulin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('carbs_grams', 6, 1)->nullable();
            $table->decimal('insulin_units', 5, 1)->nullable();
            $table->string('meal_type', 30)->default('other');
            $table->timestamp('logged_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->unsignedInteger('steps')->nullable();
            $table->timestamp('logged_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('water_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('amount_ml');
            $table->timestamp('logged_at');
            $table->timestamps();
        });

        Schema::create('share_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('label')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::table('health_profiles', function (Blueprint $table) {
            $table->unsignedSmallInteger('water_goal_ml')->default(2000)->after('doctor_name');
            $table->unsignedSmallInteger('daily_steps_goal')->default(8000)->after('water_goal_ml');
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn(['water_goal_ml', 'daily_steps_goal']);
        });
        Schema::dropIfExists('share_links');
        Schema::dropIfExists('water_logs');
        Schema::dropIfExists('exercise_logs');
        Schema::dropIfExists('insulin_logs');
        Schema::table('blood_sugar_readings', function (Blueprint $table) {
            $table->dropColumn(['mood', 'sleep_hours', 'stress_level']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
