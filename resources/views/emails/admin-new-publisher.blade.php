<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>New Publisher Registration — Installs Bank</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">

<div style="display:none;font-size:1px;color:#f4f6f8;line-height:1px;max-height:0;overflow:hidden;">
    New publisher {{ $publisher->name }} has verified their email and is awaiting your review.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8;">
<tr>
<td align="center" style="padding:40px 16px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

    <!-- Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#1f2937 0%,#374151 100%);border-radius:16px 16px 0 0;padding:28px 40px;text-align:center;">
            <img src="https://installsbank.com/images/installs-bank.webp"
                 alt="Installs Bank" width="140"
                 style="height:auto;display:block;margin:0 auto 12px;max-width:140px;"
                 onerror="this.style.display='none'">
            <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.7);letter-spacing:1px;text-transform:uppercase;">Admin Notification</div>
        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="background:#ffffff;padding:36px 40px 28px;">

            <!-- Badge -->
            <div style="text-align:center;margin-bottom:20px;">
                <span style="display:inline-block;background:#d1fae5;color:#065f46;font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;letter-spacing:0.3px;">
                    NEW PUBLISHER REQUEST
                </span>
            </div>

            <h1 style="margin:0 0 8px;font-size:22px;font-weight:800;color:#111827;text-align:center;line-height:1.3;">
                {{ $publisher->name }} has verified their email
            </h1>
            <p style="margin:0 0 28px;font-size:14px;color:#6b7280;text-align:center;line-height:1.6;">
                A new publisher has completed email verification and is waiting for your review.
            </p>

            <!-- Publisher details card -->
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb;margin-bottom:28px;">
                <tr>
                    <td style="padding:20px 24px;">
                        @php
                            $rows = [
                                ['label' => 'Name',     'value' => $publisher->name],
                                ['label' => 'Email',    'value' => $publisher->email],
                                ['label' => 'Website',  'value' => $publisher->website ?? '—'],
                                ['label' => 'Phone',    'value' => $publisher->phone   ?? '—'],
                                ['label' => 'Telegram', 'value' => $publisher->telegram ? '@'.$publisher->telegram : '—'],
                                ['label' => 'Registered', 'value' => $publisher->created_at->format('M d, Y H:i') . ' UTC'],
                            ];
                        @endphp
                        @foreach($rows as $row)
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}margin-bottom:{{ !$loop->last ? '12px' : '0' }};padding-bottom:{{ !$loop->last ? '12px' : '0' }};">
                            <tr>
                                <td width="110" style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;padding-top:1px;">
                                    {{ $row['label'] }}
                                </td>
                                <td style="font-size:14px;color:#111827;font-weight:500;">
                                    {{ $row['value'] }}
                                </td>
                            </tr>
                        </table>
                        @endforeach
                    </td>
                </tr>
            </table>

            <!-- CTA -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                <tr>
                    <td style="background:#01BF63;border-radius:10px;text-align:center;">
                        <a href="{{ $adminUrl }}"
                           style="display:inline-block;padding:13px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;">
                            Review Publisher
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f9fafb;border-radius:0 0 16px 16px;padding:20px 40px;border-top:1px solid #f3f4f6;">
            <p style="margin:0;font-size:12px;color:#d1d5db;text-align:center;line-height:1.7;">
                This is an automated notification — please do not reply to this email.<br>
                &copy; {{ date('Y') }} Installs Bank Admin System.
            </p>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>
