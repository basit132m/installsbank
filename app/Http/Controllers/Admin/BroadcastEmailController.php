<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BroadcastEmailController extends Controller
{
    private string $defaultBody = "Contacting from Installs Bank!

We are a PPI network that provides Download Button ADS with high payouts on every single unique click and install. Visit us at https://installsbank.com/.

You can check our CLICK RATES at https://installsbank.com/rates and INSTALL RATES at https://installsbank.com/install-rates.

Features that Keep Us Different from Others:

Only PPI Network with Built-In Android App 🚀

High Payouts on Every Click & Install 💰

Real-Time Stats in Your Dashboard 📊

Choose: Click-Based, Install-Based or Fixed Contracts ⚙️

Fast & Secure Crypto Withdrawals 🔐

Register Now: https://installsbank.com/register";

    public function index()
    {
        $this->authorizeAccess();
        return view('admin.broadcast-email.index', ['defaultBody' => $this->defaultBody]);
    }

    public function send(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'emails'  => 'required|string',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string|max:10000',
        ]);

        // Parse emails — support comma, semicolon, newline separated
        $rawEmails = preg_split('/[\s,;]+/', $data['emails']);
        $valid   = [];
        $invalid = [];

        foreach ($rawEmails as $email) {
            $email = trim($email);
            if ($email === '') continue;
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            } else {
                $invalid[] = $email;
            }
        }

        $valid = array_unique($valid);

        if (empty($valid)) {
            return back()->with('error', 'No valid email addresses found. Please check your input.')->withInput();
        }

        $sent   = 0;
        $failed = 0;

        foreach ($valid as $email) {
            try {
                Mail::to($email)->send(new BroadcastMailable($data['subject'], $data['body']));
                $sent++;
            } catch (\Exception) {
                $failed++;
            }
        }

        $msg = "Email sent to {$sent} address(es).";
        if ($failed > 0) $msg .= " {$failed} failed to send.";
        if (!empty($invalid)) $msg .= " Skipped " . count($invalid) . " invalid address(es): " . implode(', ', $invalid) . ".";

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
