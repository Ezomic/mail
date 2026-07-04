<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('email_address');
            $table->string('display_name')->nullable();

            // gmail | outlook | imap (custom)
            $table->string('provider');

            // IMAP settings (custom accounts, or resolved defaults for gmail/outlook)
            $table->string('imap_host')->nullable();
            $table->unsignedInteger('imap_port')->nullable();
            $table->string('imap_encryption')->nullable(); // ssl | tls | null
            $table->string('imap_username')->nullable();
            $table->text('imap_password')->nullable(); // encrypted, custom accounts only

            // SMTP settings
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // encrypted, custom accounts only

            // OAuth token storage (gmail / outlook)
            $table->text('oauth_access_token')->nullable();
            $table->text('oauth_refresh_token')->nullable();
            $table->timestamp('oauth_expires_at')->nullable();

            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_status')->default('idle'); // idle | syncing | error
            $table->text('sync_error')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_accounts');
    }
};
