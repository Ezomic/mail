<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_account_id')->constrained()->cascadeOnDelete();

            // Canonical (INBOX/SENT/DRAFTS/TRASH) or the literal remote name
            // for a custom folder/label.
            $table->string('local_name');
            $table->string('remote_path');

            $table->timestamps();

            $table->unique(['mail_account_id', 'remote_path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_folders');
    }
};
