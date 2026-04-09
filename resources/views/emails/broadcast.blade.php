<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="format-detection" content="telephone=no">
<title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

{{-- Preheader --}}
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;color:#f3f4f6;">
    High payouts on every click &amp; install — Join Installs Bank today and start earning.
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
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

            <p style="font-size:15px;font-weight:700;color:#111827;margin:0 0 16px;">Hello,</p>

            @php
                // Replace plain URLs and key phrases with clickable links
                $rendered = $bodyContent;

                $rendered = str_replace(
                    'https://installsbank.com/rates',
                    '<a href="https://installsbank.com/rates" style="color:#01BF63;font-weight:700;text-decoration:none;">https://installsbank.com/rates</a>',
                    $rendered
                );
                $rendered = str_replace(
                    'https://installsbank.com/install-rates',
                    '<a href="https://installsbank.com/install-rates" style="color:#01BF63;font-weight:700;text-decoration:none;">https://installsbank.com/install-rates</a>',
                    $rendered
                );
                $rendered = str_replace(
                    'https://installsbank.com/register',
                    '<a href="https://installsbank.com/register" style="color:#01BF63;font-weight:700;text-decoration:none;">https://installsbank.com/register</a>',
                    $rendered
                );
                $rendered = str_replace(
                    'https://installsbank.com/',
                    '<a href="https://installsbank.com/" style="color:#01BF63;font-weight:700;text-decoration:none;">https://installsbank.com/</a>',
                    $rendered
                );

                // Convert newlines to <br> for HTML display
                $rendered = nl2br(e(htmlspecialchars_decode($rendered)));
            @endphp

            <div style="font-size:15px;color:#374151;line-height:1.9;margin:0 0 28px;">
                {!! $rendered !!}
            </div>

        </td>
    </tr>

    {{-- Contact Us --}}
    <tr>
        <td style="background-color:#f9fafb;border-top:2px solid #f3f4f6;padding:24px 40px;">
            <p style="font-size:14px;font-weight:700;color:#374151;margin:0 0 14px;">Contact Us</p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    {{-- WhatsApp --}}
                    <td style="width:50%;vertical-align:top;padding-right:16px;padding-bottom:8px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;padding-right:10px;">
                                    <div style="width:36px;height:36px;background-color:#25D366;border-radius:50%;display:inline-block;text-align:center;line-height:36px;">
                                        <span style="color:#ffffff;font-size:18px;font-weight:700;">W</span>
                                    </div>
                                </td>
                                <td style="vertical-align:middle;">
                                    <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:3px;">WhatsApp</div>
                                    <div style="font-size:13px;color:#25D366;font-weight:600;">+1 (970) 742-6488</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    {{-- Telegram --}}
                    <td style="width:50%;vertical-align:top;padding-bottom:8px;">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align:middle;padding-right:10px;">
                                    <div style="width:36px;height:36px;background-color:#0088cc;border-radius:50%;display:inline-block;text-align:center;line-height:36px;">
                                        <span style="color:#ffffff;font-size:16px;font-weight:700;">T</span>
                                    </div>
                                </td>
                                <td style="vertical-align:middle;">
                                    <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:3px;">Telegram</div>
                                    <div style="font-size:13px;color:#0088cc;font-weight:600;">@installsbank</div>
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
