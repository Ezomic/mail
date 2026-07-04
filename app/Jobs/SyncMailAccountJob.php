<?php

namespace App\Jobs;

use App\Models\MailAccount;
use App\Services\Mail\ImapSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncMailAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    // Headers-only bulk sync is much faster than a full-body fetch, but a
    // large mailbox (thousands of messages) can still take a while the first
    // time. A retry after a timeout resumes quickly since already-synced
    // messages are skipped (unique on mail_account_id/folder/uid).
    public int $timeout = 7200;

    public function __construct(
        protected MailAccount $account,
    ) {}

    public function handle(ImapSyncService $syncService): void
    {
        if (! $this->account->is_active) {
            return;
        }

        $syncService->sync($this->account);
    }

    public function failed(\Throwable $e): void
    {
        $this->account->update([
            'sync_status' => 'error',
            'sync_error' => $e->getMessage(),
        ]);
    }
}
