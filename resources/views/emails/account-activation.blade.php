<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('Aktivasi Akun') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, Helvetica, sans-serif; color: #34395e;">
  @php
    $siteTitle = \App\Models\Setting::getValue('site_title', config('app.name', 'Stisla'));
  @endphp
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 32px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
          <tr>
            <td style="background-color: #6777ef; padding: 24px 32px; text-align: center;">
              <h1 style="margin: 0; color: #ffffff; font-size: 22px;">{{ $siteTitle }}</h1>
            </td>
          </tr>
          <tr>
            <td style="padding: 32px;">
              <h2 style="margin: 0 0 16px; font-size: 18px; color: #191d21;">Halo, {{ $user->name }}!</h2>
              <p style="margin: 0 0 16px; font-size: 14px; line-height: 1.6; color: #555;">
                Terima kasih telah mendaftar di <strong>{{ $siteTitle }}</strong>. Untuk mengaktifkan akun Anda dan mulai login, silakan klik tombol di bawah ini:
              </p>

              <div style="text-align: center; margin: 28px 0;">
                <a href="{{ $activationUrl }}" style="display: inline-block; background-color: #6777ef; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 14px; padding: 12px 28px; border-radius: 6px;">
                  Aktifkan Akun Sekarang
                </a>
              </div>

              <p style="margin: 0 0 12px; font-size: 13px; line-height: 1.6; color: #6c757d;">
                Link aktivasi ini berlaku selama <strong>24 jam</strong>. Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.
              </p>

              <p style="margin: 16px 0 0; font-size: 12px; color: #888; word-break: break-all;">
                Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                <a href="{{ $activationUrl }}" style="color: #6777ef;">{{ $activationUrl }}</a>
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color: #f8f9fa; padding: 16px 32px; text-align: center; font-size: 12px; color: #888;">
              &copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
