<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_sponsored')->default(false);
            $table->string('sponsor_name', 120)->nullable();
            $table->string('sponsored_card_size', 10)->default('small');
            $table->unsignedTinyInteger('image_focus_x')->default(50);
            $table->unsignedTinyInteger('image_focus_y')->default(50);
        });
        Schema::table('ads', function (Blueprint $table) {
            $table->string('card_size', 10)->default('small');
            $table->string('sponsor_name', 120)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ads', fn (Blueprint $table) => $table->dropColumn(['card_size', 'sponsor_name']));
        Schema::table('articles', fn (Blueprint $table) => $table->dropColumn(['is_sponsored', 'sponsor_name', 'sponsored_card_size', 'image_focus_x', 'image_focus_y']));
    }
};
