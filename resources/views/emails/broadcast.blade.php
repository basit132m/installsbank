<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
<span style="display:none;max-height:0;overflow:hidden;">{{ Str::limit(strip_tags($bodyContent), 90) }}</span>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

        {{-- Header --}}
        <tr>
          <td style="background:linear-gradient(135deg,#01BF63,#00874a);padding:32px 40px;text-align:center;">
            <img src="https://installsbank.com/images/installs-bank-mail.webp"
                 alt="Installs Bank"
                 width="480"
                 style="max-width:100%;height:auto;border-radius:10px;display:block;margin:0 auto;">
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="padding:36px 40px;">

            <p style="font-size:15px;color:#374151;line-height:1.8;margin:0 0 24px;white-space:pre-line;">{{ $bodyContent }}</p>

            {{-- CTA Button --}}
            <table cellpadding="0" cellspacing="0" style="margin:28px 0;">
              <tr>
                <td style="background:#01BF63;border-radius:10px;">
                  <a href="https://installsbank.com/register"
                     style="display:inline-block;padding:14px 36px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:Arial,sans-serif;">
                    Register Now →
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 40px;text-align:center;">
            <p style="font-size:12px;color:#9ca3af;margin:0 0 6px;">
              You received this email because you are registered with Installs Bank.
            </p>
            <p style="font-size:12px;color:#9ca3af;margin:0;">
              © {{ date('Y') }} Installs Bank &nbsp;·&nbsp;
              <a href="https://installsbank.com" style="color:#01BF63;text-decoration:none;">installsbank.com</a>
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
