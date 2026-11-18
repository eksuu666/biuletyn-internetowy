<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletin_issues', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('description', 500)->nullable();
            $table->string('pdf_path');
            $table->string('cover_path');
            $table->date('published_on')->index();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletin_issues');
    }
};
