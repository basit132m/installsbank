<?php

namespace App\Console\Commands;

use App\Models\BroadcastEmailLog;
use App\Models\EmailReply;
use App\Services\ImapService;
use Illuminate\Console\Command;

class FetchEmailReplies extends Command
{
    protected $signature   = 'email:fetch-replies';
    protected $description = 'Fetch unread replies from the IMAP inbox and store them in the database';

    public function handle(): int
    {
        try {
            $emails = (new ImapService())->fetchUnread();
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            \Log::error('FetchEmailReplies: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (empty($emails)) {
            $this->info('No new emails.');
            return self::SUCCESS;
        }

        $saved = 0;
        foreach ($emails as $email) {
            // Skip if already stored (idempotent)
            if (EmailReply::where('message_uid', $email['message_uid'])->exists()) {
                continue;
            }

            // Try to match to a broadcast log by recipient email
            $matched = BroadcastEmailLog::where('recipient_email', strtolower($email['from_email']))
                ->where('status', 'sent')
                ->latest()
                ->first();

            EmailReply::create([
                'message_uid'    => $email['message_uid'],
                'from_email'     => $email['from_email'],
                'from_name'      => $email['from_name'],
                'subject'        => $email['subject'],
                'body'           => $email['body'],
                'received_at'    => $email['received_at'],
                'is_read'        => false,
                'matched_log_id' => $matched?->id,
            ]);

            $saved++;
        }

        $this->info("Saved {$saved} new email(s).");
        return self::SUCCESS;
    }
}
