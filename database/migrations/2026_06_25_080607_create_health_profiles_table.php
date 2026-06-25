<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('target_min')->default(70);
            $table->unsignedSmallInteger('target_max')->default(140);
            $table->decimal('weight', 5, 1)->nullable();
            $table->decimal('height', 4, 2)->nullable();
            $table->string('diabetes_type')->nullable();
            $table->string('doctor_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_profiles');
    }
};
