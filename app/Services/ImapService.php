<?php

namespace App\Services;

class ImapService
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $folder;

    public function __construct()
    {
        $this->host     = env('IMAP_HOST', 'imap.hostinger.com');
        $this->port     = (int) env('IMAP_PORT', 993);
        $this->username = env('IMAP_USERNAME', env('MAIL_USERNAME', ''));
        $this->password = env('IMAP_PASSWORD', env('MAIL_PASSWORD', ''));
        $this->folder   = env('IMAP_FOLDER', 'INBOX');
    }

    public function fetchUnread(): array
    {
        if (!extension_loaded('imap')) {
            throw new \RuntimeException(
                'PHP IMAP extension is not loaded. Enable imap in php.ini or ask Hostinger to enable it.'
            );
        }

        $mailbox = sprintf('{%s:%d/imap/ssl/novalidate-cert}%s', $this->host, $this->port, $this->folder);

        $conn = @imap_open($mailbox, $this->username, $this->password, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);

        if (!$conn) {
            $err = imap_last_error();
            throw new \RuntimeException('IMAP connection failed: ' . $err);
        }

        try {
            $msgNums = imap_search($conn, 'UNSEEN');
            if (!$msgNums) {
                return [];
            }

            $emails = [];
            foreach ($msgNums as $msgNum) {
                try {
                    $header = imap_headerinfo($conn, $msgNum);
                    if (!$header) continue;

                    $messageId = trim($header->message_id ?? '');
                    if (!$messageId) {
                        // Fall back to a stable hash if no message-id
                        $messageId = md5(
                            ($header->from[0]->mailbox ?? '') .
                            ($header->from[0]->host ?? '') .
                            ($header->subject ?? '') .
                            ($header->date ?? '')
                        );
                    }

                    $fromEmail = '';
                    $fromName  = '';
                    if (!empty($header->from)) {
                        $fromEmail = ($header->from[0]->mailbox ?? '') . '@' . ($header->from[0]->host ?? '');
                        $fromName  = isset($header->from[0]->personal)
                            ? imap_utf8($header->from[0]->personal)
                            : '';
                    }

                    $subject = $header->subject ? imap_utf8($header->subject) : '(no subject)';

                    $body = $this->extractPlainText($conn, $msgNum);

                    // Mark as seen on the server so next fetch doesn't re-pull it
                    imap_setflag_full($conn, (string)$msgNum, '\\Seen');

                    $emails[] = [
                        'message_uid' => $messageId,
                        'from_email'  => $fromEmail,
                        'from_name'   => $fromName,
                        'subject'     => $subject,
                        'body'        => $body,
                        'received_at' => \Carbon\Carbon::parse($header->date),
                    ];
                } catch (\Throwable $e) {
                    \Log::warning('ImapService: failed to parse message #' . $msgNum . ': ' . $e->getMessage());
                }
            }

            return $emails;
        } finally {
            imap_close($conn, CL_EXPUNGE);
        }
    }

    private function extractPlainText($conn, int $msgNum): string
    {
        $structure = imap_fetchstructure($conn, $msgNum);

        // Simple (non-multipart) message
        if (!isset($structure->parts)) {
            $body = imap_body($conn, $msgNum);
            return $this->decode($body, $structure->encoding ?? 0);
        }

        // Multipart — walk parts to find text/plain
        foreach ($structure->parts as $idx => $part) {
            $partNum = (string)($idx + 1);

            if ($this->isTextPlain($part)) {
                $raw = imap_fetchbody($conn, $msgNum, $partNum);
                return $this->decode($raw, $part->encoding ?? 0);
            }

            // One level deeper (e.g. multipart/alternative inside multipart/mixed)
            if (isset($part->parts)) {
                foreach ($part->parts as $jdx => $subpart) {
                    if ($this->isTextPlain($subpart)) {
                        $raw = imap_fetchbody($conn, $msgNum, $partNum . '.' . ($jdx + 1));
                        return $this->decode($raw, $subpart->encoding ?? 0);
                    }
                }
            }
        }

        // Fallback: grab part 1 and strip any HTML
        $raw = imap_fetchbody($conn, $msgNum, '1');
        $text = $this->decode($raw, $structure->parts[0]->encoding ?? 0);
        return strip_tags($text);
    }

    private function isTextPlain(object $part): bool
    {
        return ($part->type ?? -1) === 0
            && strtoupper($part->subtype ?? '') === 'PLAIN';
    }

    private function decode(string $raw, int $encoding): string
    {
        return match ($encoding) {
            3       => base64_decode($raw),
            4       => quoted_printable_decode($raw),
            default => $raw,
        };
    }
}
