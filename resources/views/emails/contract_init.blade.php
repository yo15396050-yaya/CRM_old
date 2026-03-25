<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Contrat - {{ $companyName }}</title>
    <style>
        body { font-family: 'Public Sans', 'Arial', sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background-color: {{ $headerColor ?? '#000000' }}; text-align: center; padding: 30px; }
        .header img { max-height: 80px; }
        .content { padding: 40px; color: #333333; line-height: 1.6; }
        .content h2 { color: #111111; margin-top: 0; font-size: 24px; text-align: center; }
        .contract-box { background-color: #f8fafc; border-left: 4px solid {{ $headerColor ?? '#000000' }}; padding: 20px; margin: 25px 0; border-radius: 4px; }
        .contract-box p { margin: 8px 0; font-size: 15px; }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; padding: 14px 30px; background-color: {{ $headerColor ?? '#000000' }}; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; }
        .footer { background-color: #f8fafc; font-size: 13px; color: #718096; text-align: center; padding: 20px; border-top: 1px solid #edf2f7; }
        .footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logo }}" alt="{{ $companyName }}">
        </div>
        <div class="content">
            <h2>Bonjour {{ $recipientName }} !</h2>
            
            <p>Nous avons le plaisir de vous informer que votre contrat est désormais disponible sur votre espace <strong>{{ $companyName }}</strong>.</p>

            <div class="contract-box">
                <p><strong>📋 Sujet :</strong> {{ $subject }}</p>
                <p><strong>📑 Type :</strong> {{ $contractType }}</p>
                <p><strong>📅 Date :</strong> {{ $date }}</p>
                <p><strong>🔢 Référence :</strong> {{ $contractNumber }}</p>
            </div>

            <p>Nous vous invitons à le consulter et à le signer électroniquement en cliquant sur le bouton ci-dessous :</p>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Consulter le contrat</a>
            </div>

            <p style="margin-top: 30px;">Besoin d'aide ? Notre équipe reste à votre entière disposition par email ou par téléphone.</p>
            <p>L’équipe <strong>{{ $companyName }}</strong></p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ $companyName }} – Tous droits réservés.</p>
            <p>📧 {{ $company->company_email }} | 📞 {{ $company->company_phone }}</p>
        </div>
    </div>
</body>
</html>
