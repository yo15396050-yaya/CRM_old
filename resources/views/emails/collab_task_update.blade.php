{{--
  Mise a jour d'une tache adressee a un collaborateur.
  Rendu par TaskCommunicationNotification (branche collaborateur).

  Le changement de statut est l'information utile : il est donc porte par la
  transition de deux pastilles .dck-badge, la grise « en attente » vers la
  bleue « actif ». Tables et styles inline uniquement.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $taskHeading }}</title>
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
            @if(!empty($taskReference))
                <td align="right" valign="middle" style="font-family:Consolas,Menlo,monospace;font-size:12.5px;color:#9ca3af;">{{ $taskReference }}</td>
            @endif
        </tr></table>
    </td></tr>

    <tr><td style="padding:26px 28px 0 28px;">
        <h1 style="margin:0;font-size:20px;line-height:1.3;color:#111827;font-weight:bold;letter-spacing:-.5px;">{{ $taskHeading }}</h1>
        <p style="margin:10px 0 0;font-size:14.5px;line-height:1.65;color:#374151;">
            Bonjour {{ $recipientName }}, le statut de cette tâche vient de changer.
        </p>
    </td></tr>

    @if(!empty($previousStatus) && $previousStatus !== $taskStatus)
        <tr><td style="padding:22px 28px 0 28px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
                <tr><td style="padding:20px 22px;">
                    <table cellpadding="0" cellspacing="0" border="0"><tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0" style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:999px;"><tr>
                                <td style="padding:4px 12px;font-size:12px;font-weight:600;color:#6b7280;">{{ $previousStatus }}</td>
                            </tr></table>
                        </td>
                        <td style="padding:0 12px;font-size:15px;color:#9ca3af;">&rarr;</td>
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0" style="background:#eff8ff;border:1px solid #b3ddf7;border-radius:999px;"><tr>
                                <td style="padding:4px 12px;font-size:12px;font-weight:600;color:#0369a1;">{{ $taskStatus }}</td>
                            </tr></table>
                        </td>
                    </tr></table>
                </td></tr>
            </table>
        </td></tr>
    @endif

    @if(!empty($noteContent))
        <tr><td style="padding:22px 28px 0 28px;">
            <div style="font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;margin-bottom:9px;">Note de mise à jour</div>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb;border-radius:12px;">
                <tr><td style="padding:16px 18px;font-size:14px;line-height:1.65;color:#374151;">{!! nl2br(e($noteContent)) !!}</td></tr>
                <tr><td style="padding:0 18px 14px 18px;font-size:12px;color:#9ca3af;">
                    {{ $modifiedBy }} · aujourd'hui à {{ $heure }}
                </td></tr>
            </table>
        </td></tr>
    @endif

    <tr><td style="padding:22px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Client</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $clientName }}</td>
            </tr>
            @if(!empty($projectName))
                <tr>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Dossier</td>
                    <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $projectName }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Échéance</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $dueDate }}@if(!empty($dueLabel))<span style="color:{{ $dueOverdue ? '#b42318' : '#a16207' }};font-weight:600;"> · {{ $dueLabel }}</span>@endif</td>
            </tr>
        </table>
    </td></tr>

    <tr><td style="padding:26px 28px 30px 28px;">
        <table cellpadding="0" cellspacing="0" border="0"><tr>
            <td style="background:#000000;border-radius:8px;">
                <a href="{{ $url }}" style="display:inline-block;padding:12px 22px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;">Voir la tâche</a>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:18px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;font-size:12px;line-height:1.7;color:#6b7280;">
        Vous recevez ce message car vous êtes affecté à cette tâche.
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
