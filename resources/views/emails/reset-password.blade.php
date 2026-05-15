<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Reset Your Password</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f4f8;padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;">

          {{-- Logo / Brand --}}
          {{-- <tr>
            <td align="center" style="padding-bottom:24px;">
              <img
                src="{{ rtrim(config('app.url'), '/') }}/favicon/SIPRT.png"
                alt="{{ config('app.name') }}"
                width="80"
                height="80"
                style="display:block;object-fit:contain;background:#ffffff;border-radius:16px;padding:8px;"
              >
            </td>
          </tr> --}}

          {{-- Card --}}
          <tr>
            <td style="background:#ffffff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);overflow:hidden;">

              {{-- Blue top bar --}}
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="background:linear-gradient(135deg,#1976D2,#0D47A1);height:6px;font-size:0;line-height:0;">&nbsp;</td>
                </tr>
              </table>

              {{-- Body --}}
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="padding:40px 40px 32px;">

                    {{-- Greeting --}}
                    <p style="margin:0 0 8px;font-size:13px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#1976D2;">
                      Password Reset
                    </p>
                    <h1 style="margin:0 0 16px;font-size:24px;font-weight:700;color:#0f172a;line-height:1.3;">
                      Hi, {{ $userName }}
                    </h1>
                    <p style="margin:0 0 28px;font-size:15px;color:#475569;line-height:1.7;">
                      We received a request to reset the password for your
                      <strong style="color:#0f172a;">{{ config('app.name') }}</strong> account.
                      Click the button below to choose a new password.
                    </p>

                    {{-- CTA Button --}}
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td align="center" style="padding-bottom:28px;">
                          <a
                            href="{{ $resetUrl }}"
                            style="
                              display:inline-block;
                              padding:14px 36px;
                              background:linear-gradient(135deg,#1976D2,#0D47A1);
                              color:#ffffff;
                              text-decoration:none;
                              border-radius:10px;
                              font-size:15px;
                              font-weight:700;
                              letter-spacing:.03em;
                              box-shadow:0 4px 14px rgba(21,101,192,.35);
                            "
                          >
                            Reset My Password
                          </a>
                        </td>
                      </tr>
                    </table>

                    {{-- Expiry notice --}}
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                      <tr>
                        <td style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;">
                          <p style="margin:0;font-size:13px;color:#64748b;line-height:1.6;">
                            &#x26a0;&#xfe0f;&nbsp;
                            This link will expire in <strong style="color:#0f172a;">{{ $expiresIn }} minutes</strong>.
                            If you didn't request a password reset, you can safely ignore this email.
                          </p>
                        </td>
                      </tr>
                    </table>

                    {{-- Fallback URL --}}
                    <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                      If the button doesn't work, copy and paste this URL into your browser:
                    </p>
                    <p style="margin:6px 0 0;font-size:12px;word-break:break-all;">
                      <a href="{{ $resetUrl }}" style="color:#1976D2;text-decoration:none;">{{ $resetUrl }}</a>
                    </p>

                  </td>
                </tr>
              </table>

            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td align="center" style="padding-top:24px;">
              <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.7;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
              </p>
              <p style="margin:4px 0 0;font-size:12px;color:#cbd5e1;">
                This email was sent automatically — please do not reply.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
