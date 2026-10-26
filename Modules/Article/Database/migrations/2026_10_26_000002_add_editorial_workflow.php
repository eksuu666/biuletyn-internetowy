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
            $table->string('workflow_state', 32)->default('draft')->index();
            $table->unsignedInteger('workflow_version')->default(1);
            $table->foreignId('corrected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('corrected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });
        // Preserve existing publications without inventing historical approvals.
        DB::table('articles')->where('status', true)->update(['workflow_state' => 'published']);
        Schema::create('article_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40);
            $table->string('from_state', 32)->nullable();
            $table->string('to_state', 32);
            $table->text('note')->nullable();
            $table->json('snapshot');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_revisions');
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['workflow_state']);
            $table->dropConstrainedForeignId('corrected_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['workflow_state', 'workflow_version', 'corrected_at', 'approved_at']);
        });
    }
};
