<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Verify your email — Installs Bank</title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<!-- Preheader (hidden preview text) -->
<div style="display:none;font-size:1px;color:#f4f6f8;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    Welcome to Installs Bank! Please verify your email address to complete your registration.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8;">
<tr>
<td align="center" style="padding:40px 16px;">

    <!-- Email Card -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

        <!-- Header -->
        <tr>
            <td style="background:linear-gradient(135deg,#01BF63 0%,#00a354 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
                <img src="https://installsbank.com/images/installs-bank.webp"
                     alt="Installs Bank"
                     width="160"
                     style="height:auto;display:block;margin:0 auto 16px;max-width:160px;"
                     onerror="this.style.display='none'">
                <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.85);letter-spacing:1px;text-transform:uppercase;">Email Verification</div>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="background:#ffffff;padding:40px 40px 32px;">

                <!-- Welcome image -->
                <div style="text-align:center;margin-bottom:28px;">
                    <img src="https://installsbank.com/images/waving-fox.webp"
                         alt="Welcome!"
                         width="120"
                         style="height:auto;display:inline-block;max-width:120px;"
                         onerror="this.style.display='none'">
                </div>

                <h1 style="margin:0 0 8px;font-size:24px;font-weight:800;color:#111827;text-align:center;line-height:1.3;">
                    Welcome to Installs Bank!
                </h1>
                <p style="margin:0 0 24px;font-size:15px;color:#6b7280;text-align:center;line-height:1.6;">
                    Hi <strong style="color:#111827;">{{ $user->name }}</strong>, thanks for signing up.<br>
                    Please verify your email address to go forward.
                </p>

                <!-- CTA Button -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 28px;">
                    <tr>
                        <td style="background:#01BF63;border-radius:10px;text-align:center;">
                            <a href="{{ $verifyUrl }}"
                               style="display:inline-block;padding:14px 36px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.2px;line-height:1;">
                                Verify Email Address
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Expiry notice -->
                <p style="margin:0 0 24px;font-size:13px;color:#9ca3af;text-align:center;line-height:1.6;">
                    This link expires in <strong>72 hours</strong>. If it expires, you can request a new one from the login page.
                </p>

                <!-- Divider -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
                    <tr><td style="border-top:1px solid #f3f4f6;"></td></tr>
                </table>

                <!-- Fallback URL -->
                <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.7;">
                    If the button above doesn't work, copy and paste this link into your browser:<br>
                    <a href="{{ $verifyUrl }}" style="color:#01BF63;word-break:break-all;font-size:12px;">{{ $verifyUrl }}</a>
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#f9fafb;border-radius:0 0 16px 16px;padding:24px 40px;border-top:1px solid #f3f4f6;">
                <p style="margin:0 0 8px;font-size:12px;color:#9ca3af;text-align:center;line-height:1.7;">
                    If you didn't create an account with Installs Bank, you can safely ignore this email.
                </p>
                <p style="margin:0;font-size:12px;color:#d1d5db;text-align:center;">
                    This is an automated message — please do not reply to this email.<br>
                    &copy; {{ date('Y') }} Installs Bank. All rights reserved.
                </p>
            </td>
        </tr>

    </table>
    <!-- /Email Card -->

</td>
</tr>
</table>

</body>
</html>
