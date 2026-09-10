{{--
  Mise a disposition d'un contrat pour lecture et signature electronique.
  Rendu par ContractInitNotification.

  Ce message porte un lien signe a expiration : le delai est donc affiche en
  clair dans le tableau, et le caractere personnel du lien rappele sous le
  bouton. L'ancien gabarit reposait sur un bloc <style> a base de classes,
  degrade par Outlook et par l'application Gmail iOS.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
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
                <table cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:999px;"><tr>
                    <td style="padding:4px 11px;font-size:11.5px;font-weight:600;color:#374151;">Signature électronique</td>
                </tr></table>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:26px 28px 0 28px;">
        <h1 style="margin:0;font-size:21px;line-height:1.3;color:#111827;font-weight:bold;letter-spacing:-.5px;">{{ $subject }}</h1>
        <p style="margin:12px 0 0;font-size:14.5px;line-height:1.65;color:#374151;">
            Bonjour {{ $recipientName }}, votre contrat est prêt. Vous pouvez le lire intégralement puis le signer en ligne, sans rien imprimer ni renvoyer.
        </p>
    </td></tr>

    <tr><td style="padding:22px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="36%" style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Référence</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-family:Consolas,Menlo,monospace;font-size:13.5px;color:#111827;font-weight:bold;">{{ $contractNumber }}</td>
            </tr>
            <tr>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Type de contrat</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $contractType }}</td>
            </tr>
            <tr>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">Établi le</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;font-size:13.5px;color:#111827;font-weight:600;">{{ $date }}</td>
            </tr>
            <tr>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#6b7280;">À signer avant le</td>
                <td style="padding:13px 0;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;font-size:13.5px;color:#a16207;font-weight:bold;">{{ $expiryDate }}</td>
            </tr>
        </table>
    </td></tr>

    <tr><td style="padding:26px 28px 0 28px;">
        <table cellpadding="0" cellspacing="0" border="0"><tr>
            <td style="background:#000000;border-radius:8px;">
                <a href="{{ $url }}" style="display:inline-block;padding:13px 24px;color:#ffffff;text-decoration:none;font-size:14.5px;font-weight:600;">Lire et signer le contrat</a>
            </td>
        </tr></table>
    </td></tr>

    <tr><td style="padding:20px 28px 0 28px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef9e7;border:1px solid #f3dfa2;border-radius:12px;">
            <tr><td style="padding:15px 18px;font-size:13.5px;line-height:1.65;color:#a16207;">
                <strong>Ce lien vous est personnel</strong> et reste valable {{ $expiryDays }}&nbsp;jours. Ne le transférez pas : toute signature apposée depuis ce lien vous sera attribuée. Passé ce délai, demandez-nous simplement un nouvel envoi.
            </td></tr>
        </table>
    </td></tr>

    <tr><td style="padding:22px 28px 30px 28px;font-size:14.5px;line-height:1.7;color:#374151;">
        Une question sur une clause avant de signer ? Répondez à cet e-mail.
    </td></tr>

    <tr><td style="padding:18px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;font-size:12px;line-height:1.7;color:#6b7280;">
        {{ $companyName }}@if(!empty($company->company_email)) · {{ $company->company_email }}@endif @if(!empty($company->company_phone))· {{ $company->company_phone }}@endif
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
