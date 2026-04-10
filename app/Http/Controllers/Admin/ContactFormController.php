<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactFormSubmission;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    private const DAILY_LIMIT = 20;

    private string $defaultMessage = "We are reaching out about a publisher partnership opportunity with Installs Bank.

Installs Bank is a pay-per-install network where publishers place our download buttons on their websites and get paid for every click and install generated.

What we offer:
- Click-based and install-based payment contracts
- Real-time statistics dashboard
- Built-in Android app for publishers
- Crypto withdrawals
- Fixed contract option for stable income

If you have website traffic and are interested in monetizing it, we would love to discuss a partnership.

Contact us on WhatsApp at +1 (970) 742-6488 or Telegram @installsbank.";

    public function index()
    {
        $this->authorizeAccess();
        $todayCount = ContactFormSubmission::whereDate('created_at', today())->count();
        $recent     = ContactFormSubmission::latest()->limit(30)->get();
        return view('admin.contact-form.index', [
            'todayCount'     => $todayCount,
            'dailyLimit'     => self::DAILY_LIMIT,
            'recent'         => $recent,
            'defaultMessage' => $this->defaultMessage,
        ]);
    }

    public function submit(Request $request)
    {
        $this->authorizeAccess();

        $data = $request->validate([
            'url'     => 'required|url|max:500',
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:200',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
        ]);

        $todayCount = ContactFormSubmission::whereDate('created_at', today())->count();
        if ($todayCount >= self::DAILY_LIMIT) {
            return back()->with('error', 'Daily limit of ' . self::DAILY_LIMIT . ' submissions reached. Try again tomorrow.')->withInput();
        }

        $result = $this->findAndSubmit($data);

        ContactFormSubmission::create([
            'url'          => $data['url'],
            'sender_name'  => $data['name'],
            'sender_email' => $data['email'],
            'status'       => $result['success'] ? 'sent' : 'failed',
            'note'         => $result['message'],
        ]);

        $flashKey = $result['success'] ? 'success' : 'error';
        return back()->with($flashKey, $result['message'])->withInput();
    }

    // -------------------------------------------------------------------------

    private function findAndSubmit(array $data): array
    {
        try {
            $jar    = new CookieJar();
            $client = new Client([
                'timeout'         => 20,
                'connect_timeout' => 10,
                'verify'          => false,
                'allow_redirects' => true,
                'cookies'         => $jar,
                'headers'         => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ],
            ]);

            // Fetch the page
            $response = $client->get($data['url']);
            $html     = (string) $response->getBody();

            // Parse
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            // Find all forms on the page
            $forms = $xpath->query('//form');
            if (!$forms || $forms->length === 0) {
                return ['success' => false, 'message' => 'No forms found on this page. Try providing the direct URL of the contact page.'];
            }

            // Pick the best contact form
            $bestForm   = null;
            $bestFields = [];

            foreach ($forms as $form) {
                $fields = $this->extractFields($xpath, $form);
                if ($this->isContactForm($fields)) {
                    $bestForm   = $form;
                    $bestFields = $fields;
                    break;
                }
            }

            if (!$bestForm) {
                return ['success' => false, 'message' => 'No contact form detected on this page. The form may be JavaScript-rendered (not supported) or require a CAPTCHA.'];
            }

            // Resolve form action URL
            $action = trim($bestForm->getAttribute('action'));
            $method = strtoupper($bestForm->getAttribute('method') ?: 'POST');

            if ($action === '' || $action === '#') {
                $action = $data['url'];
            } elseif (!str_starts_with($action, 'http')) {
                $parsed = parse_url($data['url']);
                $base   = $parsed['scheme'] . '://' . $parsed['host'];
                $action = $base . '/' . ltrim($action, '/');
            }

            // Build POST data
            $postData = $this->buildPostData($bestFields, $data);

            // Submit
            $submitResponse = $client->request($method, $action, [
                'form_params' => $postData,
                'headers'     => [
                    'Referer'       => $data['url'],
                    'Origin'        => parse_url($data['url'], PHP_URL_SCHEME) . '://' . parse_url($data['url'], PHP_URL_HOST),
                ],
            ]);

            $code = $submitResponse->getStatusCode();
            if ($code >= 200 && $code < 400) {
                return ['success' => true, 'message' => 'Message submitted successfully.'];
            }

            return ['success' => false, 'message' => 'Form submitted but server returned HTTP ' . $code . '.'];

        } catch (RequestException $e) {
            $msg = $e->hasResponse()
                ? 'HTTP ' . $e->getResponse()->getStatusCode() . ' error from the website.'
                : 'Could not connect: ' . $e->getMessage();
            return ['success' => false, 'message' => $msg];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function extractFields(\DOMXPath $xpath, \DOMElement $form): array
    {
        $fields  = [];
        $inputs  = $xpath->query('.//input | .//textarea | .//select', $form);

        foreach ($inputs as $el) {
            $tag   = strtolower($el->tagName);
            $type  = strtolower($el->getAttribute('type') ?: 'text');
            $name  = $el->getAttribute('name');
            $id    = strtolower($el->getAttribute('id'));
            $class = strtolower($el->getAttribute('class'));
            $value = $tag === 'textarea' ? $el->textContent : $el->getAttribute('value');

            if (!$name) continue;

            $fields[] = compact('tag', 'type', 'name', 'id', 'class', 'value');
        }

        return $fields;
    }

    private function isContactForm(array $fields): bool
    {
        $hasEmail   = false;
        $hasMessage = false;

        foreach ($fields as $f) {
            if ($this->isEmailField($f))   $hasEmail   = true;
            if ($this->isMessageField($f)) $hasMessage = true;
        }

        return $hasEmail && $hasMessage;
    }

    private function buildPostData(array $fields, array $data): array
    {
        $post    = [];
        $subject = $data['subject'] ?: 'Partnership Opportunity — Installs Bank';

        foreach ($fields as $f) {
            $key = $f['name'];

            if ($f['type'] === 'hidden') {
                $post[$key] = $f['value']; // Keep CSRF tokens etc.
            } elseif ($this->isEmailField($f)) {
                $post[$key] = $data['email'];
            } elseif ($this->isNameField($f)) {
                $post[$key] = $data['name'];
            } elseif ($this->isMessageField($f)) {
                $post[$key] = $data['message'];
            } elseif ($this->isSubjectField($f)) {
                $post[$key] = $subject;
            } elseif ($f['type'] === 'submit') {
                $post[$key] = $f['value'] ?: 'Submit';
            } elseif ($f['tag'] === 'select') {
                $post[$key] = $f['value']; // Keep default selected option
            }
            // skip checkboxes/radios/file inputs
        }

        return $post;
    }

    private function isEmailField(array $f): bool
    {
        $h = strtolower($f['name'] . ' ' . $f['id'] . ' ' . $f['class']);
        return $f['type'] === 'email'
            || str_contains($h, 'email')
            || str_contains($h, 'e-mail');
    }

    private function isNameField(array $f): bool
    {
        $h = strtolower($f['name'] . ' ' . $f['id'] . ' ' . $f['class']);
        return str_contains($h, 'your-name')
            || str_contains($h, 'full-name')
            || str_contains($h, 'fullname')
            || str_contains($h, 'first-name')
            || str_contains($h, 'firstname')
            || str_contains($h, 'last-name')
            || str_contains($h, 'lastname')
            || str_contains($h, 'author')
            || ($f['type'] === 'text' && $h === 'name name');
    }

    private function isMessageField(array $f): bool
    {
        if ($f['tag'] === 'textarea') return true;
        $h = strtolower($f['name'] . ' ' . $f['id'] . ' ' . $f['class']);
        return str_contains($h, 'message')
            || str_contains($h, 'comment')
            || str_contains($h, 'msg')
            || str_contains($h, 'body')
            || str_contains($h, 'content')
            || str_contains($h, 'description');
    }

    private function isSubjectField(array $f): bool
    {
        $h = strtolower($f['name'] . ' ' . $f['id'] . ' ' . $f['class']);
        return str_contains($h, 'subject')
            || str_contains($h, 'topic')
            || str_contains($h, 'regarding');
    }

    private function authorizeAccess(): void
    {
        if (auth()->user()->role !== 'admin') abort(403);
    }
}
