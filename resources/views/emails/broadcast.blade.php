<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

{{-- Preheader (hidden preview text in inbox) --}}
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#f3f4f6;line-height:1px;">
    High payouts on every click &amp; install — Join Installs Bank today and start earning.
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;">
<tr>
<td align="center" style="padding:32px 16px;">

<table width="600" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;">

    {{-- Header image --}}
    <tr>
        <td style="background:linear-gradient(135deg,#01BF63,#00874a);padding:28px 32px;text-align:center;">
            <img src="https://installsbank.com/images/installs-bank-mail.webp"
                 alt="Installs Bank — High Payout PPI Network"
                 width="520" border="0"
                 style="max-width:100%;height:auto;border-radius:10px;display:block;margin:0 auto;">
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:36px 40px 28px;">

            {{-- Greeting / custom intro --}}
            <p style="font-size:15px;color:#374151;line-height:1.8;margin:0 0 24px;white-space:pre-line;">{{ $bodyContent }}</p>

            {{-- Visit us --}}
            <p style="font-size:15px;color:#374151;line-height:1.8;margin:0 0 10px;">
                Visit us at
                <a href="https://installsbank.com/" style="color:#01BF63;text-decoration:none;font-weight:700;">installsbank.com</a>
            </p>

            {{-- Rates links --}}
            <p style="font-size:15px;color:#374151;line-height:1.8;margin:0 0 24px;">
                Check our&nbsp;
                <a href="https://installsbank.com/rates" style="color:#01BF63;font-weight:700;text-decoration:none;">Click Rates</a>
                &nbsp;and&nbsp;
                <a href="https://installsbank.com/install-rates" style="color:#01BF63;font-weight:700;text-decoration:none;">Install Rates</a>.
            </p>

            {{-- Divider --}}
            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                <tr><td style="border-top:2px solid #f3f4f6;"></td></tr>
            </table>

            {{-- Features heading --}}
            <p style="font-size:16px;font-weight:700;color:#111827;margin:0 0 14px;">
                What Makes Us Different
            </p>

            {{-- Feature rows --}}
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                @foreach([
                    ['🚀', 'Only PPI Network with Built-In Android App', 'Your publishers can track installs from our dedicated Android app — no third-party tools needed.'],
                    ['💰', 'High Payouts on Every Click &amp; Install', 'Competitive rates paid on every unique valid click and verified app install from your traffic.'],
                    ['📊', 'Real-Time Stats Dashboard', 'Watch your clicks, installs, and earnings update live — full transparency, always.'],
                    ['⚙️', 'Flexible Contract Types', 'Choose Click-Based, Install-Based, or Fixed Daily Rate — whichever suits your traffic best.'],
                    ['🔐', 'Fast &amp; Secure Crypto Withdrawals', 'Get paid quickly via crypto. Minimum thresholds are low and withdrawals are processed promptly.'],
                ] as $feat)
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #f9fafb;vertical-align:top;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:36px;vertical-align:top;padding-top:2px;">
                                    <span style="font-size:20px;">{{ $feat[0] }}</span>
                                </td>
                                <td style="vertical-align:top;">
                                    <div style="font-size:14px;font-weight:700;color:#111827;margin-bottom:3px;">{!! $feat[1] !!}</div>
                                    <div style="font-size:13px;color:#6b7280;line-height:1.6;">{!! $feat[2] !!}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
            </table>

            {{-- CTA button --}}
            <table border="0" cellpadding="0" cellspacing="0" style="margin:32px auto;display:block;text-align:center;">
                <tr>
                    <td align="center" style="background-color:#01BF63;border-radius:12px;">
                        <a href="https://installsbank.com/register"
                           style="display:inline-block;padding:15px 44px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;letter-spacing:0.3px;">
                            Register Now — It's Free
                        </a>
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    {{-- Contact Us section --}}
    <tr>
        <td style="background-color:#f9fafb;border-top:2px solid #f3f4f6;padding:24px 40px;">
            <p style="font-size:14px;font-weight:700;color:#374151;margin:0 0 14px;">Contact Us</p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    {{-- WhatsApp --}}
                    <td style="padding-right:12px;width:50%;vertical-align:top;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;padding-right:10px;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/120px-WhatsApp.svg.png"
                                         width="28" height="28" alt="WhatsApp" style="display:block;">
                                </td>
                                <td style="vertical-align:middle;">
                                    <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:2px;">WhatsApp</div>
                                    <a href="https://wa.me/19707426488?text=Hello%20Installs%20Bank%20team%2C%20I%27m%20interested%20in%20joining%20your%20network."
                                       style="font-size:12px;color:#25D366;text-decoration:none;font-weight:600;">
                                        Chat on WhatsApp
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                    {{-- Telegram --}}
                    <td style="width:50%;vertical-align:top;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;padding-right:10px;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Telegram_logo.svg/120px-Telegram_logo.svg.png"
                                         width="28" height="28" alt="Telegram" style="display:block;">
                                </td>
                                <td style="vertical-align:middle;">
                                    <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:2px;">Telegram</div>
                                    <a href="https://t.me/installsbank?text=Hello%20Installs%20Bank%20team%2C%20I%27m%20interested%20in%20joining%20your%20network."
                                       style="font-size:12px;color:#0088cc;text-decoration:none;font-weight:600;">
                                        Message on Telegram
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background-color:#111827;padding:20px 40px;text-align:center;border-radius:0 0 16px 16px;">
            <p style="font-size:13px;color:#9ca3af;margin:0 0 6px;">
                <a href="https://installsbank.com/" style="color:#01BF63;text-decoration:none;font-weight:600;">installsbank.com</a>
                &nbsp;·&nbsp;
                <a href="https://installsbank.com/rates" style="color:#9ca3af;text-decoration:none;">Click Rates</a>
                &nbsp;·&nbsp;
                <a href="https://installsbank.com/install-rates" style="color:#9ca3af;text-decoration:none;">Install Rates</a>
                &nbsp;·&nbsp;
                <a href="https://installsbank.com/register" style="color:#9ca3af;text-decoration:none;">Register</a>
            </p>
            <p style="font-size:12px;color:#6b7280;margin:0;">
                &copy; {{ date('Y') }} Installs Bank. This email was sent to you as a potential publisher partner.
            </p>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>
