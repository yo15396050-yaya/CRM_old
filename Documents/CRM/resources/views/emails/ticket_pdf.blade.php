<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Confirmation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .ticket {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h3 {
            color: rgb(251, 255, 0);
            margin: 0;
            font-size: 24px;
            background-color: #000;
            padding: 5px 0;
        }
        .header p {
            margin: 5px 0;
            color: #555;
        }
        .details {
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-label {
            font-weight: bold;
            width: 180px;
            color: #333;
        }
        .formations-list {
            background-color: #fff3cd;
            border-left: 4px solid #ffcc00;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .formation-item {
            padding: 10px 0;
            border-bottom: 1px solid #ffeaa7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .formation-item:last-child {
            border-bottom: none;
        }
        .formation-name {
            font-weight: 600;
            color: #000;
        }
        .formation-places {
            display: inline-block;
            background-color: #ffcc00;
            color: #000;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .formation-date {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }
        .qr-code {
            margin: 20px 0;
            padding: 10px;
            border: 1px dashed #ffcc00;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: space-between;
        }
        .qr-text {
            flex: 1;
        }
        .qr-image {
            flex-shrink: 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('img/logo-dark.png') }}" alt="Logo" style="width:150px;">
            <h3>{{ $title_form }}</h3>
            <p>Abidjan, Cocody, Riviera Bonoumin non loin du collège André Malraux</p>
        </div>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">N° Ticket:</span>
                <span>{{ $ticket_number }}</span>
            </div>
            
            <!-- Affichage des formations multiples -->
            @php
                $formations = json_decode($label_formation, true);
                $isArray = is_array($formations) && isset($formations[0]['nom']);
            @endphp
            
            @if($isArray && count($formations) > 0)
                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #333; margin-bottom: 10px;">Formations :</div>
                    <div class="formations-list">
                        @foreach($formations as $formation)
                            <div class="formation-item">
                                <div style="flex: 1;">
                                    <div class="formation-name">{{ $formation['nom'] ?? 'Formation' }}</div>
                                    <div class="formation-date">{{ $formation['date'] ?? 'À confirmer' }}</div>
                                </div>
                                <span class="formation-places">{{ $formation['nombre_places'] ?? 1 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="detail-row">
                    <span class="detail-label">Formation:</span>
                    <span>{{ $label_formation }}</span>
                </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Nom Complet:</span>
                <span>{{ $nom_complet }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span>{{ $email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Téléphone:</span>
                <span>{{ $telephone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nombre de places:</span>
                <span>{{ $nombre_tickets }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Montant total payé:</span>
                <span>{{ $montant_total }}</span>
            </div>  
        </div>
        
        <div class="qr-code" style="display: flex; align-items: center;" align="center">
            <div class="qr-text" style="margin-right: 20px;">
                <strong>Code Ticket :</strong> {{ $ticket_number }}<br>
                <small>Scannez le QR code ou visitez :</small><br>
            </div>
            <div class="qr-image">
                <img src="{{ $qr_code_base64 }}" alt="Code QR" style="width: 100px; height: 100px;" />
            </div>
        </div>
        
        <div class="footer">
            <p>Émis le: {{ $date_emission }}</p>
            <p>Contact: +225 27 22 42 14 43 | infos@dcknowing.com</p>
        </div>
    </div>
</body>
</html>
