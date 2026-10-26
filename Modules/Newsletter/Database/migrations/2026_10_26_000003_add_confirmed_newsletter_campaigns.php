<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->string('confirmation_hash', 64)->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('confirmed_at')->nullable()->index();
            $table->timestamp('unsubscribed_at')->nullable();
        });
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 200);
            $table->text('body');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
        Schema::create('newsletter_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('newsletter_campaigns')->cascadeOnDelete();
            $table->foreignId('newsletter_id')->constrained('newsletters')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'newsletter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_deliveries');
        Schema::dropIfExists('newsletter_campaigns');
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropIndex(['confirmed_at']);
            $table->dropColumn(['confirmation_hash', 'consented_at', 'confirmed_at', 'unsubscribed_at']);
        });
    }
};
