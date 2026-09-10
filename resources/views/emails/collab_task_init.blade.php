{{--
  Assignation d'une tache a un collaborateur.
  Rendu par TaskInitNotification (branche collaborateur).

  Mise en page reprise du design systeme du CRM (public/css/dck-flow.css) :
  neutres --n50 a --n900, noir pur en action, rayon 12 px sur les cartes et
  8 px sur les boutons, pastilles de statut a bordure fine.

  Contrainte e-mail : tables et styles inline uniquement. Ni <style>, ni flex,
  ni police distante — Gmail et Outlook les suppriment ou les ignorent.
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

    {{-- En-tete : logo PNG (le SVG est supprime par Gmail) et nature du message --}}
    <tr><td style="padding:22px 28px;border-bottom:1px solid #e5e7eb;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
            <td valign="middle">
                {{-- Image embarquee (cid) : le logo ne depend alors plus de APP_URL --}}
                <img src="{{ isset($message) && !empty($logoPath) && file_exists($logoPath) ? $message->embed($logoPath) : $logoUrlDark }}" alt="{{ $companyName }}" width="118" height="30" style="display:block;border:0;width:118px;height:30px;">
            </td>
            <td align="right" valign="middle">
                <table cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;"><tr>
                    <td style="padding:4px 11px;font-size:11.5px;font-weight:600;color:#374151;">Nouvelle assignation</td>
                </tr></table>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:28px 28px 0 28px;">
        <h1 style="margin:0;font-size:21px;line-height:1.3;color:#111827;font-weight:bold;letter-spacing:-.5px;">{{ $taskHeading }}</h1>
        <p style="margin:12px 0 0;font-size:14.5px;line-height:1.65;color:#374151;">
            Bonjour {{ $recipientName }}, cette tâche vous est confiée. Les éléments du dossier sont récapitulés ci-dessous.
        </p>
        @if(!empty($dueLabel))
            {{-- Ambre quand l'echeance approche, rouge quand elle est depassee --}}
            <table cellpadding="0" cellspacing="0" border="0" style="margin-top:16px;background:{{ $dueOverdue ? '#fdf1f0' : '#fef9e7' }};border:1px solid {{ $dueOverdue ? '#f2c9c6' : '#f3dfa2' }};border-radius:999px;"><tr>
                <td style="padding:4px 12px;font-size:12px;font-weight:600;color:{{ $dueOverdue ? '#b42318' : '#a16207' }};">{{ $dueLabel }}</td>
            </tr></table>
        @endif
    </td></tr>

    <tr><td style="padding:24px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb;border-radius:12px;">
            <tr>
                <td style="padding:11px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;">Élément</td>
                <td style="padding:11px 16px;background:#f9fafb;border-bottom:1px solid #e5e7eb;font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;">Valeur</td>
            </tr>
            @if(!empty($taskReference))
                <tr>
                    <td width="36%" style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Référence</td>
                    <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-family:Consolas,Menlo,monospace;font-size:13.5px;color:#111827;font-weight:bold;">{{ $taskReference }}</td>
                </tr>
            @endif
            <tr>
                <td width="36%" style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Client</td>
                <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:bold;">{{ $clientName }}</td>
            </tr>
            @if(!empty($projectName))
                <tr>
                    <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Dossier</td>
                    <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:bold;">{{ $projectName }}</td>
                </tr>
            @endif
            @if(!empty($responsibleName))
                <tr>
                    <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Responsable</td>
                    <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:bold;">{{ $responsibleName }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Priorité</td>
                <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:bold;">{{ $priority }}</td>
            </tr>
            <tr>
                <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Échéance</td>
                <td style="padding:13px 16px;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:bold;">{{ $dueDate }}</td>
            </tr>
            <tr>
                <td style="padding:13px 16px;font-size:13.5px;color:#6b7280;">Statut</td>
                <td style="padding:13px 16px;">
                    <table cellpadding="0" cellspacing="0" border="0" style="background:#eff8ff;border:1px solid #b3ddf7;border-radius:999px;"><tr>
                        <td style="padding:3px 10px;font-size:11.5px;font-weight:600;color:#0369a1;">{{ $taskStatus }}</td>
                    </tr></table>
                </td>
            </tr>
        </table>
    </td></tr>

    @if(!empty($description))
        <tr><td style="padding:24px 28px 0 28px;">
            <div style="font-size:11.5px;font-weight:bold;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;margin-bottom:9px;">Instructions</div>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
                <tr><td style="padding:16px 18px;font-size:14px;line-height:1.65;color:#374151;">{!! $description !!}</td></tr>
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
                <a href="{{ $url }}" style="display:inline-block;padding:12px 22px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;">Ouvrir la tâche</a>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:18px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;font-size:12px;line-height:1.7;color:#6b7280;">
        Notification interne {{ $companyName }} — répondez à ce message pour joindre le responsable du dossier.
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
