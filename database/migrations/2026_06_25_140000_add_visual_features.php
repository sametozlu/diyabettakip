<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_plans', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('menu_items');
        });

        Schema::table('health_profiles', function (Blueprint $table) {
            $table->string('cover_photo')->nullable()->after('doctor_name');
            $table->string('avatar_photo')->nullable()->after('cover_photo');
            $table->boolean('onboarding_done')->default(false)->after('avatar_photo');
        });

        Schema::table('medications', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('notes');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('location');
        });

        Schema::create('progress_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->decimal('value', 8, 2);
            $table->string('photo_path')->nullable();
            $table->date('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_snapshots');
        Schema::table('appointments', fn (Blueprint $t) => $t->dropColumn('image_url'));
        Schema::table('medications', fn (Blueprint $t) => $t->dropColumn('photo_path'));
        Schema::table('health_profiles', fn (Blueprint $t) => $t->dropColumn(['cover_photo', 'avatar_photo', 'onboarding_done']));
        Schema::table('meal_plans', fn (Blueprint $t) => $t->dropColumn('image_url'));
    }
};
