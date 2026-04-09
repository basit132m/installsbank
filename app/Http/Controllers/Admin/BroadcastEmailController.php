<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMailable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BroadcastEmailController extends Controller
{
    private string $defaultBody = "Hello,

Contacting from Installs Bank!

We are a PPI network that provides Download Button ADS with high payouts on every single unique click and install. Visit us at https://installsbank.com/.

You can check our CLICK RATES at https://installsbank.com/rates and INSTALL RATES at https://installsbank.com/install-rates.

Features that Keep Us Different from Others:

* Only PPI Network with Built-In Android App 🚀
* High Payouts on Every Click & Install 💰
* Real-Time Stats in Your Dashboard 📊
* Choose: Click-Based, Install-Based or Fixed Contracts ⚙️
* Fast & Secure Crypto Withdrawals 🔐

Register Now: https://installsbank.com/register";

    public function index()
    {
        $this->authorizeAccess();

        $publishers = User::where('role', 'publisher')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'status']);

        $defaultBody = $this->defaultBody;

        return view('admin.broadcast-email.index', compact('publishers', 'defaultBody'));
    }

    public function send(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'recipients'    => 'required|in:all,active,specific',
            'specific_ids'  => 'required_if:recipients,specific|array',
            'specific_ids.*'=> 'exists:users,id',
            'subject'       => 'required|string|max:200',
            'body'          => 'required|string|max:10000',
        ]);

        $query = User::where('role', 'publisher');

        if ($data['recipients'] === 'active') {
            $query->where('status', 'active');
        } elseif ($data['recipients'] === 'specific') {
            $query->whereIn('id', $data['specific_ids']);
        }

        $publishers = $query->get(['id', 'name', 'email']);

        if ($publishers->isEmpty()) {
            return back()->with('error', 'No publishers found for the selected recipients.')->withInput();
        }

        $sent   = 0;
        $failed = 0;

        foreach ($publishers as $publisher) {
            try {
                Mail::to($publisher->email)
                    ->send(new BroadcastMailable($publisher->name, $data['subject'], $data['body']));
                $sent++;
            } catch (\Exception) {
                $failed++;
            }
        }

        $msg = "Email sent to {$sent} publisher(s).";
        if ($failed > 0) {
            $msg .= " {$failed} failed.";
        }

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
