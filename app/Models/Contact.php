<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'name',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record (or refresh) a contact from an observed email address, e.g. seen
     * on an inbound message or an outbound send.
     */
    public static function remember(int $userId, string $email, ?string $name = null): void
    {
        $email = trim(strtolower($email));

        if ($email === '' || ! str_contains($email, '@')) {
            return;
        }

        $contact = static::firstOrNew(['user_id' => $userId, 'email' => $email]);
        $contact->name = $name ?: $contact->name;
        $contact->last_seen_at = now();
        $contact->save();
    }
}
