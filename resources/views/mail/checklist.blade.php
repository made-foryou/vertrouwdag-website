{{--
    The checklist mail (App\Checklist\Mail\ChecklistMail), html version. Mail clients ignore
    stylesheets, so the styling is inline, in the colours of the site. The button has
    data-pm-no-track, so Postmark never rewrites the signed link, even with link tracking on.
--}}
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('checklist.mail.subject') }}</title>
</head>
<body style="margin:0;padding:0;background:#F6F6F0;color:#2d331f;font-family:Georgia,'Times New Roman',serif">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F6F0">
    <tr>
        <td align="center" style="padding:32px 16px">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#FBFBF7;border-radius:12px">
                <tr>
                    <td style="padding:32px 32px 8px;font-size:15px;letter-spacing:.12em;text-transform:uppercase;color:#401838;font-family:Arial,Helvetica,sans-serif">vertrouwdag</td>
                </tr>
                <tr>
                    <td style="padding:8px 32px 0;font-size:19px;line-height:1.6">
                        <p style="margin:0 0 16px">{{ __('checklist.mail.greeting', ['name' => $firstName]) }}</p>
                        <p style="margin:0 0 24px">{{ __('checklist.mail.intro') }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 24px">
                        <a href="{{ $checklistUrl }}" data-pm-no-track style="display:inline-block;padding:14px 28px;border-radius:999px;background:#404830;color:#F6F6F0;text-decoration:none;font-family:Arial,Helvetica,sans-serif;font-size:15px">{{ __('checklist.mail.button') }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 24px;font-size:14px;line-height:1.6;color:#626655;font-family:Arial,Helvetica,sans-serif">
                        <p style="margin:0 0 4px">{{ __('checklist.mail.fallback') }}</p>
                        <p style="margin:0;word-break:break-all"><a href="{{ $checklistUrl }}" data-pm-no-track style="color:#401838">{{ $checklistUrl }}</a></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 32px 32px;font-size:19px;line-height:1.6">
                        <p style="margin:0">{{ __('checklist.mail.closing') }}<br>{{ __('checklist.mail.signature') }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px 32px 32px;border-top:1px solid rgba(64,72,48,.12);font-size:13px;line-height:1.6;color:#626655;font-family:Arial,Helvetica,sans-serif">
                        @foreach (__('checklist.mail.why') as $sentence)
                            {{ $sentence }}
                        @endforeach
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
