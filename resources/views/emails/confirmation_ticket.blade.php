<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Ticket de Confirmation</title>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 40px 20px;
            color: #1e293b;
        }
        .ticket {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 180px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            background-color: #0f172a;
            padding: 12px 20px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 10px 0 0;
            color: #64748b;
            font-weight: 500;
        }
        .details {
            margin: 30px 0;
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
        }
        .detail-value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .qr-code {
            margin: 30px 0;
            padding: 25px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        .qr-text {
            flex: 1;
            color: #475569;
        }
        .qr-text strong {
            display: block;
            font-size: 18px;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .qr-image img {
            width: 140px;
            height: 140px;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('storage/Logo.png') }}" alt="Logo" />
            <h1>FORMATION BÉNÉFICIAIRES EFFECTIFS</h1>
            <p>Samedi 30 Août 2025 - 8h à 12h</p>
            <p>Cocody, Abidjan</p>
        </div>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">N° Ticket</span>
                <span class="detail-value">{{ $ticket_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nom Complet</span>
                <span class="detail-value">{{ $nom_complet }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Téléphone</span>
                <span class="detail-value">{{ $telephone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nombre de places</span>
                <span class="detail-value">{{ $nombre_tickets }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Montant payé</span>
                <span class="detail-value" style="color: #059669;">{{ $montant_total }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">ID Transaction</span>
                <span class="detail-value" style="font-family: monospace; font-size: 12px;">{{ $transaction_id }}</span>
            </div>
        </div>
        
        <div class="qr-code">
            <div class="qr-text">
                <strong>Ticket #{{ $ticket_number }}</strong>
                <p style="margin: 0; font-size: 12px;">Présentez ce code QR à l'entrée pour valider votre participation.</p>
            </div>
            <div class="qr-image">
                <img src="{{ $qr_code_base64 }}" alt="Code QR" />
            </div>
        </div>
        
        <div class="footer">
            <p>Émis le {{ $date_emission }}</p>
            <p>Support : +225 27 22 42 14 43 | infos@dcknowing.com</p>
        </div>
    </div>
</body>
</html>
