<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('storage_path'); // path on the 'local' disk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
