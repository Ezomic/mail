<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_account_id')->constrained()->cascadeOnDelete();

            $table->string('message_id')->nullable()->index(); // RFC message-id, dedupe key
            $table->string('folder')->default('INBOX')->index(); // INBOX | SENT | DRAFTS | TRASH ...
            $table->string('uid')->nullable(); // IMAP UID within folder

            $table->string('subject')->nullable();
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->text('to_addresses')->nullable();   // JSON array
            $table->text('cc_addresses')->nullable();   // JSON array
            $table->longText('body_html')->nullable();
            $table->longText('body_text')->nullable();

            $table->boolean('is_read')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->timestamp('sent_at')->nullable()->index();

            $table->timestamps();

            $table->unique(['mail_account_id', 'folder', 'uid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
