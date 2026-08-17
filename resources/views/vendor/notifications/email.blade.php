<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $subject ?? 'GrowPOS' }}</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f3f4f6; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    {{-- Preheader: teks preview yang muncul di inbox, tersembunyi dari tampilan email --}}
    <div
        style="display:none; max-height:0px; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#f3f4f6;">
        {{ $preheader ?? ($introLines[0] ?? 'Notifikasi dari GrowPOS System') }}
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f3f4f6; padding: 32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#065f46; padding:28px 32px; text-align:center;">
                            <img src="{{ config('app.url') }}/img/growpos_logo-bg.png" alt="GrowPOS"
                                style="height:40px; width:40px; border-radius:50%; display:block; margin:0 auto 8px;">
                            <div style="color:#ffffff; font-size:16px; font-weight:700; letter-spacing:0.3px;">GrowPOS
                                System</div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 32px;">

                            @if (!empty($greeting))
                                <h1 style="margin:0 0 16px; font-size:20px; font-weight:700; color:#111827;">
                                    {{ $greeting }}</h1>
                            @else
                                <h1 style="margin:0 0 16px; font-size:20px; font-weight:700; color:#111827;">Halo!</h1>
                            @endif

                            @foreach ($introLines as $line)
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.6; color:#374151;">
                                    {{ $line }}</p>
                            @endforeach

                            @isset($actionText)
                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto;">
                                    <tr>
                                        <td align="center" style="border-radius:8px; background-color:#059669;">
                                            <a href="{{ $actionUrl }}" target="_blank"
                                                style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:8px;">
                                                {{ $actionText }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endisset

                            @foreach ($outroLines as $line)
                                <p style="margin:0 0 14px; font-size:15px; line-height:1.6; color:#374151;">
                                    {{ $line }}</p>
                            @endforeach

                            @isset($securityInfo)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="margin:20px 0; background-color:#fef3c7; border-radius:8px; border:1px solid #fde68a;">
                                    <tr>
                                        <td style="padding:16px 18px;">
                                            <p style="margin:0 0 8px; font-size:13px; font-weight:700; color:#92400e;">
                                                🔒 Detail Permintaan
                                            </p>
                                            <p style="margin:0 0 4px; font-size:13px; color:#78350f; line-height:1.6;">
                                                Waktu: {{ $securityInfo['time'] ?? '-' }}<br>
                                                Alamat IP: {{ $securityInfo['ip'] ?? '-' }}<br>
                                                Perangkat: {{ $securityInfo['device'] ?? '-' }}
                                            </p>
                                            <p style="margin:8px 0 0; font-size:12px; color:#92400e;">
                                                Bukan Anda yang melakukan ini? Segera amankan akun Anda atau hubungi tim
                                                support kami.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endisset

                            <p style="margin:24px 0 0; font-size:15px; line-height:1.6; color:#374151;">
                                @if (!empty($salutation))
                                    {!! nl2br(e($salutation)) !!}
                                @else
                                    Salam hangat,<br>
                                    <strong>Tim Ekosistem GrowPOS Indonesia</strong>
                                @endif
                            </p>

                        </td>
                    </tr>

                    {{-- Subcopy (fallback link) --}}
                    @isset($actionText)
                        <tr>
                            <td style="padding:0 32px 28px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="border-top:1px solid #e5e7eb; padding-top:20px;">
                                    <tr>
                                        <td>
                                            <p style="margin:0 0 8px; font-size:13px; color:#6b7280; line-height:1.5;">
                                                Jika Anda mengalami kendala saat menekan tombol
                                                "<strong>{{ $actionText }}</strong>", silakan salin dan tempel URL
                                                berikut ke browser Anda:
                                            </p>
                                            <p style="margin:0; font-size:12px; color:#059669; word-break:break-all;">
                                                <a href="{{ $actionUrl }}"
                                                    style="color:#059669; text-decoration:underline;">{{ $displayableActionUrl }}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endisset

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background-color:#f9fafb; padding:28px 32px; text-align:center; border-top:1px solid #e5e7eb;">

                            <p style="margin:0 0 12px; font-size:13px; color:#4b5563;">
                                Butuh bantuan? <a
                                    href="{{ config('app.support_url', $actionUrl ?? config('app.url')) }}"
                                    style="color:#059669; text-decoration:none; font-weight:600;">Hubungi Tim Support
                                    GrowPOS</a>
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 16px;">
                                <tr>
                                    @if (config('services.social.instagram'))
                                        <td style="padding:0 6px;">
                                            <a href="{{ config('services.social.instagram') }}"
                                                style="color:#6b7280; text-decoration:none; font-size:12px;">Instagram</a>
                                        </td>
                                    @endif
                                    @if (config('services.social.whatsapp'))
                                        <td style="padding:0 6px; border-left:1px solid #d1d5db;">
                                            <a href="{{ config('services.social.whatsapp') }}"
                                                style="color:#6b7280; text-decoration:none; font-size:12px; padding-left:12px;">WhatsApp</a>
                                        </td>
                                    @endif
                                    @if (config('services.social.website', config('app.url')))
                                        <td style="padding:0 6px; border-left:1px solid #d1d5db;">
                                            <a href="{{ config('app.url') }}"
                                                style="color:#6b7280; text-decoration:none; font-size:12px; padding-left:12px;">Website</a>
                                        </td>
                                    @endif
                                </tr>
                            </table>

                            <p style="margin:0 0 6px; font-size:11px; color:#9ca3af; line-height:1.5;">
                                {{ config('app.company_name', 'GrowPOS Indonesia') }}<br>
                                {{ config('app.company_address', 'Surabaya, Jawa Timur, Indonesia') }}
                            </p>

                            <p style="margin:0; font-size:11px; color:#9ca3af;">
                                &copy; {{ date('Y') }} GrowPOS. All rights reserved.
                            </p>

                            @isset($unsubscribeUrl)
                                <p style="margin:10px 0 0; font-size:11px;">
                                    <a href="{{ $unsubscribeUrl }}"
                                        style="color:#9ca3af; text-decoration:underline;">Kelola preferensi notifikasi</a>
                                </p>
                            @endisset

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
