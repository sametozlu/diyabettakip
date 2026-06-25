<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('plan_date');
            $table->string('day_name');
            $table->string('week_label')->nullable();
            $table->text('menu_items');
            $table->json('eat_items');
            $table->json('reduce_items');
            $table->json('skip_items');
            $table->timestamps();

            $table->unique(['user_id', 'plan_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
