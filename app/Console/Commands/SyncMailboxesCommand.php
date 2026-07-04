<?php

namespace App\Console\Commands;

use App\Jobs\SyncMailAccountJob;
use App\Models\MailAccount;
use Illuminate\Console\Command;

class SyncMailboxesCommand extends Command
{
    protected $signature = 'mail:sync {--account= : Sync a single MailAccount by ID}';
    protected $description = 'Dispatch IMAP sync jobs for active mail accounts';

    public function handle(): int
    {
        $query = MailAccount::query()->where('is_active', true);

        if ($accountId = $this->option('account')) {
            $query->where('id', $accountId);
        }

        $accounts = $query->get();

        foreach ($accounts as $account) {
            SyncMailAccountJob::dispatch($account);
            $this->info("Queued sync for {$account->email_address}");
        }

        return self::SUCCESS;
    }
}
