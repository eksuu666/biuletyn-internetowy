<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_tracks', function (Blueprint $table) {
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->index(['created_at', 'article_id', 'user_track_id'], 'request_tracks_article_window');
        });
    }

    public function down(): void
    {
        Schema::table('request_tracks', function (Blueprint $table) {
            $table->dropIndex('request_tracks_article_window');
            $table->dropConstrainedForeignId('article_id');
        });
    }
};
