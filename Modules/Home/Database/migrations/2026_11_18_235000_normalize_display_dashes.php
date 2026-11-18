<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            'articles' => ['title', 'body'],
            'bulletin_issues' => ['title', 'description'],
            'site_details' => ['title', 'description', 'keywords', 'footer_text'],
            'about_us' => ['title', 'content'],
            'pages' => ['title', 'content'],
            'categories' => ['name', 'description'],
            'tags' => ['name'],
            'menus' => ['name'],
            'seo_settings' => ['meta_title', 'meta_description', 'meta_keywords', 'meta_author'],
            'users' => ['full_name', 'bio'],
            'comments' => ['comment'],
            'forum_topics' => ['title', 'body'],
            'forum_replies' => ['body'],
            'contact_info' => ['title', 'content', 'address'],
            'campaigns' => ['subject', 'body'],
            'images' => ['alt_text'],
        ];
        $replacements = ["\u{2014}" => '-', "\u{2013}" => '-', '&mdash;' => '-', '&ndash;' => '-', '&#8212;' => '-', '&#8211;' => '-', '&#x2014;' => '-', '&#x2013;' => '-'];

        DB::transaction(function () use ($fields, $replacements) {
            foreach ($fields as $table => $columns) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $columns = array_values(array_intersect($columns, Schema::getColumnListing($table)));
                if ($columns === []) {
                    continue;
                }
                DB::table($table)->select(['id', ...$columns])->orderBy('id')->chunkById(200, function ($rows) use ($table, $columns, $replacements) {
                    foreach ($rows as $row) {
                        $changes = [];
                        foreach ($columns as $column) {
                            if (! is_string($row->$column)) {
                                continue;
                            }
                            // Preserve links and other HTML attributes while normalizing visible text.
                            $parts = preg_split('/(<[^>]+>)/u', $row->$column, -1, PREG_SPLIT_DELIM_CAPTURE);
                            foreach ($parts as $index => $part) {
                                if ($index % 2 === 0) {
                                    $parts[$index] = strtr($part, $replacements);
                                }
                            }
                            $value = implode('', $parts);
                            if ($value !== $row->$column) {
                                $changes[$column] = $value;
                            }
                        }
                        if ($changes !== []) {
                            DB::table($table)->where('id', $row->id)->update($changes);
                        }
                    }
                });
            }
        });
    }

    public function down(): void
    {
        // The original punctuation cannot be inferred from the normalized text.
    }
};
