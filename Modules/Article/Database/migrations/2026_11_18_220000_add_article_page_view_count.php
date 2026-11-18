<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('page_view_count')->default(0);
        });

        DB::statement("UPDATE articles SET page_view_count = (
            SELECT COUNT(*) FROM request_tracks
            WHERE request_tracks.article_id = articles.id AND request_tracks.tag = 'page_view'
        )");
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('page_view_count');
        });
    }
};
