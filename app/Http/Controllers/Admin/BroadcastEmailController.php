<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMailable;
use App\Models\BroadcastEmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BroadcastEmailController extends Controller
{
    private string $defaultBody = "We are reaching out about a publisher partnership opportunity with Installs Bank.

Installs Bank is a pay-per-install network where publishers place our download buttons on their websites and get paid for every click and install generated.

What we offer:
- Click-based and install-based contracts
- Real-time statistics dashboard
- Built-in Android app for publishers
- Crypto withdrawals
- Fixed contract option for stable income

If you have website traffic and are interested in monetizing it, we would love to discuss a partnership.

Contact us on WhatsApp at +1 (970) 742-6488 or Telegram @installsbank and we will get back to you shortly.";

    public function index()
    {
        $this->authorizeAccess();

        $history = BroadcastEmailLog::with('sender')
            ->latest()
            ->paginate(50);

        return view('admin.broadcast-email.index', [
            'defaultBody' => $this->defaultBody,
            'history'     => $history,
        ]);
    }

    public function send(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'emails'       => 'required|string',
            'subject'      => 'required|string|max:200',
            'body'         => 'required|string|max:10000',
            'force_resend' => 'nullable|in:0,1',
        ]);

        $rawEmails = preg_split('/[\s,;]+/', $data['emails']);
        $valid   = [];
        $invalid = [];

        foreach ($rawEmails as $email) {
            $email = trim($email);
            if ($email === '') continue;
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = strtolower($email);
            } else {
                $invalid[] = $email;
            }
        }

        $valid = array_values(array_unique($valid));

        if (empty($valid)) {
            return back()->with('error', 'No valid email addresses found.')->withInput();
        }

        // Duplicate check — skip if user forced resend
        if (($data['force_resend'] ?? '0') !== '1') {
            $duplicates = BroadcastEmailLog::whereIn('recipient_email', $valid)
                ->where('status', 'sent')
                ->where('created_at', '>=', now()->subDays(30))
                ->get(['recipient_email', 'subject', 'created_at'])
                ->groupBy('recipient_email')
                ->map(fn($rows) => $rows->sortByDesc('created_at')->first())
                ->values()
                ->toArray();

            if (!empty($duplicates)) {
                return back()
                    ->withInput()
                    ->with('duplicate_warning', $duplicates);
            }
        }

        $batchId = (string) Str::uuid();
        $sentBy  = auth()->id();
        $sent    = 0;
        $failed  = 0;
        $lastError = null;

        foreach ($valid as $email) {
            try {
                Mail::to($email)->send(new BroadcastMailable($data['subject'], $data['body']));
                BroadcastEmailLog::create([
                    'sent_by'         => $sentBy,
                    'batch_id'        => $batchId,
                    'recipient_email' => $email,
                    'subject'         => $data['subject'],
                    'status'          => 'sent',
                ]);
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                $lastError = $e->getMessage();
                BroadcastEmailLog::create([
                    'sent_by'         => $sentBy,
                    'batch_id'        => $batchId,
                    'recipient_email' => $email,
                    'subject'         => $data['subject'],
                    'status'          => 'failed',
                    'error_message'   => $e->getMessage(),
                ]);
                \Log::error('BroadcastEmail send failed', ['email' => $email, 'error' => $e->getMessage()]);
            }
        }

        $msg = "Email sent to {$sent} address(es).";
        if ($failed > 0) $msg .= " {$failed} failed.";
        if ($lastError)  $msg .= " Last error: " . $lastError;
        if (!empty($invalid)) $msg .= " Skipped " . count($invalid) . " invalid: " . implode(', ', $invalid) . ".";

        return back()->with('success', $msg);
    }

    private function authorizeAccess(): void
    {
        $user = auth()->user();
        if ($user->role === 'admin') return;
        if (!$user->hasPermission('can_send_broadcast_emails')) {
            abort(403, 'You do not have permission to send broadcast emails.');
        }
    }
}
