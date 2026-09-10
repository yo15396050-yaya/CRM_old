{{--
  Ouverture d'un dossier / affectation d'un collaborateur a un projet.
  Rendu par ProjectInitNotification (branche collaborateur).

  L'ancien gabarit n'annoncait que le nom du projet, et posait sa mise en page
  sur display:flex et un <link> Google Fonts — tous deux ignores par Gmail.
  Celui-ci ouvre le dossier et n'utilise que des tables et des styles inline.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $projectName }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f4f6;padding:32px 12px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;">

    <tr><td style="padding:22px 28px;border-bottom:1px solid #e5e7eb;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
            <td valign="middle">
                {{-- Image embarquee (cid) : le logo ne depend alors plus de APP_URL --}}
                <img src="{{ isset($message) && !empty($logoPath) && file_exists($logoPath) ? $message->embed($logoPath) : $logoUrlDark }}" alt="{{ $companyName }}" width="118" height="30" style="display:block;border:0;width:118px;height:30px;">
            </td>
            <td align="right" valign="middle">
                <table cellpadding="0" cellspacing="0" border="0" style="background:#f2f8e9;border:1px solid #cbe0a8;border-radius:999px;"><tr>
                    <td style="padding:4px 11px;font-size:11.5px;font-weight:600;color:#3f6212;">Ouverture de dossier</td>
                </tr></table>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:26px 28px 0 28px;">
        @if(!empty($clientName))
            <div style="font-size:12.5px;color:#6b7280;">{{ $clientName }}</div>
        @endif
        <h1 style="margin:6px 0 0;font-size:22px;line-height:1.28;color:#111827;font-weight:bold;letter-spacing:-.6px;">{{ $projectName }}</h1>
        <p style="margin:12px 0 0;font-size:14.5px;line-height:1.65;color:#374151;">
            Bonjour {{ $recipientName }}, vous intervenez sur ce dossier. En voici le cadre avant votre première intervention.
        </p>
    </td></tr>

    <tr><td style="padding:24px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            @if(!empty($projectCode))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Référence</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-family:Consolas,Menlo,monospace;font-size:13.5px;color:#111827;font-weight:bold;">{{ $projectCode }}</td>
                </tr>
            @endif
            @if(!empty($natureName))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Nature</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $natureName }}</td>
                </tr>
            @endif
            @if(!empty($periodLabel))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Période couverte</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $periodLabel }}</td>
                </tr>
            @endif
            @if(!empty($deadline))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Échéance du dossier</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $deadline }}</td>
                </tr>
            @endif
            @if(!empty($clientName))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Client</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $clientName }}</td>
                </tr>
            @endif
            @if(!empty($adminName))
                <tr>
                    <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Responsable du dossier</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $adminName }}</td>
                </tr>
            @endif
            <tr>
                <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Affectation</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $date }}</td>
            </tr>
        </table>
    </td></tr>

    @if(!empty($teamMembers))
        <tr><td style="padding:22px 28px 0 28px;">
            <div style="font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;margin-bottom:10px;">Équipe affectée</div>
            <table cellpadding="0" cellspacing="0" border="0"><tr>
                @foreach($teamMembers as $member)
                    <td style="padding-right:6px;padding-bottom:6px;">
                        <table cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;"><tr>
                            <td style="padding:3px 10px;font-size:11.5px;font-weight:600;color:#374151;">{{ $member }}</td>
                        </tr></table>
                    </td>
                @endforeach
            </tr></table>
        </td></tr>
    @endif

    @if(!empty($summary))
        <tr><td style="padding:22px 28px 0 28px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
                <tr><td style="padding:16px 18px;font-size:14px;line-height:1.65;color:#374151;">{!! $summary !!}</td></tr>
            </table>
        </td></tr>
    @endif

    @if(!empty($attachments))
        <tr><td style="padding:22px 28px 0 28px;">
            <div style="font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;margin-bottom:4px;">Pièces du dossier</div>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach($attachments as $attachment)
                    <tr><td style="padding:11px 0;{{ $loop->last ? '' : 'border-bottom:1px solid #e5e7eb;' }}font-size:13.5px;">
                        <a href="{{ $attachment['url'] }}" style="color:#111827;font-weight:600;">{{ $attachment['name'] }}</a>
                    </td></tr>
                @endforeach
            </table>
        </td></tr>
    @endif

    <tr><td style="padding:26px 28px 30px 28px;">
        <table cellpadding="0" cellspacing="0" border="0"><tr>
            <td style="background:#000000;border-radius:8px;">
                <a href="{{ $url }}" style="display:inline-block;padding:12px 22px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;">Accéder au dossier</a>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:18px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;font-size:12px;line-height:1.7;color:#6b7280;">
        Affectation interne {{ $companyName }}. Les informations de ce dossier sont couvertes par le secret professionnel.
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
