<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'articles_publication_index');
            $table->index(['category_id', 'status', 'published_at'], 'articles_category_publication_index');
            $table->index(['user_id', 'status', 'published_at'], 'articles_author_publication_index');
        });
    }

    public function down(): void
    {
        // MariaDB may replace an implicit FK index with the covering index.
        // Restore standalone indexes before removing the covering indexes.
        foreach (['category_id', 'user_id'] as $column) {
            if (! Schema::hasIndex('articles', [$column])) {
                Schema::table('articles', fn (Blueprint $table) => $table->index($column));
            }
        }
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_publication_index');
            $table->dropIndex('articles_category_publication_index');
            $table->dropIndex('articles_author_publication_index');
        });
    }
};
