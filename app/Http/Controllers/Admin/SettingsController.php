<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    /** Keys allowed to be updated via admin panel */
    private const ALLOWED_KEYS = [
        'MAIL_MAILER',
        'MAIL_HOST',
        'MAIL_PORT',
        'MAIL_USERNAME',
        'MAIL_PASSWORD',
        'MAIL_ENCRYPTION',
        'MAIL_FROM_ADDRESS',
        'MAIL_FROM_NAME',
        'APP_NAME',
        'APP_URL',
        // IMAP (incoming email for replies inbox)
        'IMAP_HOST',
        'IMAP_PORT',
        'IMAP_USERNAME',
        'IMAP_PASSWORD',
        'IMAP_FOLDER',
    ];

    public function index()
    {
        $settings = [];
        foreach (self::ALLOWED_KEYS as $key) {
            $settings[$key] = env($key, '');
        }

        $withdrawalDays = json_decode(Setting::get('withdrawal_days', '[0,6]'), true);
        $announcements  = Announcement::with('creator')->latest()->get();

        $contactInfo = [
            'whatsapp' => Setting::get('contact_whatsapp', ''),
            'telegram' => Setting::get('contact_telegram', ''),
            'email'    => Setting::get('contact_email', ''),
        ];

        return view('admin.settings.index', compact('settings', 'withdrawalDays', 'announcements', 'contactInfo'));
    }

    public function updateWithdrawalDays(Request $request)
    {
        $days = $request->input('withdrawal_days', []);

        // Validate: must be array of 0-6
        $days = array_values(array_filter(array_map('intval', (array) $days), fn($d) => $d >= 0 && $d <= 6));

        Setting::set('withdrawal_days', json_encode($days));

        return back()->with('success', 'Withdrawal days updated successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'MAIL_MAILER'       => 'required|in:smtp,sendmail,mailgun,ses,log',
            'MAIL_HOST'         => 'nullable|string|max:255',
            'MAIL_PORT'         => 'nullable|integer',
            'MAIL_USERNAME'     => 'nullable|string|max:255',
            'MAIL_ENCRYPTION'   => 'nullable|in:tls,ssl,starttls,',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME'    => 'required|string|max:100',
        ]);

        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return back()->withErrors(['error' => '.env file not found.']);
        }

        $envContent = file_get_contents($envPath);

        foreach (self::ALLOWED_KEYS as $key) {
            if (!$request->has($key)) continue;

            $value = $request->input($key, '');

            // Wrap in quotes if value contains spaces or special chars
            $needsQuotes = preg_match('/[\s#&]/', $value) || ($key === 'MAIL_FROM_NAME');
            $formattedValue = $needsQuotes ? '"' . addslashes($value) . '"' : $value;

            // Replace existing key
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $envContent);
            } else {
                // Append if key doesn't exist
                $envContent .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $envContent);

        // Clear config cache so new values take effect
        Artisan::call('config:clear');

        return back()->with('success', 'Settings saved successfully.');
    }

    public function updateImap(Request $request)
    {
        $request->validate([
            'IMAP_HOST'     => 'required|string|max:255',
            'IMAP_PORT'     => 'required|integer',
            'IMAP_USERNAME' => 'required|string|max:255',
            'IMAP_PASSWORD' => 'nullable|string|max:255',
            'IMAP_FOLDER'   => 'nullable|string|max:100',
        ]);

        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return back()->withErrors(['error' => '.env file not found.']);
        }

        $envContent = file_get_contents($envPath);
        $imapKeys   = ['IMAP_HOST', 'IMAP_PORT', 'IMAP_USERNAME', 'IMAP_PASSWORD', 'IMAP_FOLDER'];

        foreach ($imapKeys as $key) {
            $value          = $request->input($key, '');
            $needsQuotes    = preg_match('/[\s#&]/', $value);
            $formattedValue = $needsQuotes ? '"' . addslashes($value) . '"' : $value;

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $envContent);
        Artisan::call('config:clear');

        return back()->with('success', 'IMAP settings saved successfully.');
    }

    public function updateContactInfo(Request $request)
    {
        $data = $request->validate([
            'whatsapp' => 'nullable|string|max:50',
            'telegram' => 'nullable|string|max:100',
            'email'    => 'nullable|email|max:255',
        ]);

        Setting::set('contact_whatsapp', trim($data['whatsapp'] ?? ''));
        Setting::set('contact_telegram', ltrim(trim($data['telegram'] ?? ''), '@'));
        Setting::set('contact_email',    trim($data['email'] ?? ''));

        return back()->with('success', 'Contact info updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            Mail::raw('This is a test email from Installs Bank admin panel. Your email settings are working correctly!', function ($msg) use ($request) {
                $msg->to($request->test_email)
                    ->subject('Installs Bank — Test Email');
            });

            return back()->with('success', 'Test email sent to ' . $request->test_email . '. Please check your inbox.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }
}
