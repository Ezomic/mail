<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    protected $fillable = [
        'user_id',
        'mail_account_id',
        'to_addresses',
        'cc_addresses',
        'subject',
        'body',
        'in_reply_to',
        'references_header',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mailAccount(): BelongsTo
    {
        return $this->belongsTo(MailAccount::class);
    }
}
