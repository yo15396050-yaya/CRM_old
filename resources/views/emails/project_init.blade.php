<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Public Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #3d4852; 
            line-height: 1.6; 
            background-color: #f4f7f6; 
            margin: 0; 
            padding: 40px 20px; 
        }
        
        .header-top { text-align: center; margin-bottom: 25px; }
        .company-name { 
            color: {{ $company->header_color }}; 
            font-size: 24px; 
            font-weight: bold; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 5px; 
        }
        .header-line { border-top: 1px solid #e1e4e8; width: 150px; margin: 10px auto; }
        .meta-info { font-size: 13px; color: #718096; margin-top: 5px; }
        
        .main-card { 
            background-color: #ffffff; 
            width: 100%; 
            max-width: 650px; 
            margin: 0 auto; 
            border-radius: 12px; 
            border: 1px solid #e1e4e8; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            overflow: hidden; 
        }
        .card-content { padding: 40px; }
        
        .objet { 
            font-size: 22px; 
            font-weight: 700; 
            color: #1a202c; 
            margin-bottom: 30px; 
            border-bottom: 2px solid {{ $company->header_color }}22; 
            padding-bottom: 20px; 
        }
        .greeting { font-size: 18px; margin-bottom: 20px; color: #2d3748; font-weight: 600; }
        
        .note-title { font-weight: 700; margin-bottom: 12px; font-size: 16px; color: #1a202c; }
        .note-box { 
            background-color: #f8fafc; 
            border: 1px solid #edf2f7; 
            border-radius: 10px; 
            padding: 24px; 
            color: #4a5568; 
            font-size: 15px; 
            margin-bottom: 30px; 
        }
        
        .footer-text { margin-top: 40px; color: #718096; font-size: 14px; }
        .signature { margin-top: 35px; font-size: 15px; color: #2d3748; }
        .signature-name { 
            font-weight: 700; 
            font-size: 18px; 
            margin-top: 8px; 
            color: {{ $company->header_color }}; 
            text-transform: uppercase; 
        }

        .attachments-section { border-top: 1px solid #edf2f7; padding-top: 25px; margin-top: 20px; }
        .attachments-title { font-weight: bold; font-size: 15px; color: #2d3748; margin-bottom: 15px; }
        
        .attachments-list { list-style: none; padding: 0; margin: 0; }
        .attachment-item { margin-bottom: 12px; font-size: 15px; display: flex; align-items: center; }
        .attachment-bullet { color: {{ $company->header_color }}; margin-right: 12px; font-size: 18px; }
        .attachment-link { color: {{ $company->header_color }}; text-decoration: none; font-weight: 500; }
        .attachment-link:hover { text-decoration: underline; }

        .btn-container { text-align: center; margin-top: 35px; }
        .btn { 
            background-color: {{ $company->header_color }}; 
            color: #ffffff !important; 
            padding: 14px 40px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            display: inline-block; 
            font-size: 15px; 
            box-shadow: 0 4px 6px {{ $company->header_color }}44;
        }
    </style>
</head>
<body>
    <div class="header-top">
        <div class="company-name">{{ $companyName }}</div>
        <div class="header-line"></div>
        <div class="meta-info">Le {{ $date }} à {{ $heure }}</div>
    </div>

    <div class="main-card">
        <div class="card-content">
            <div class="objet">Ouverture de votre dossier : {{ $projectName }}</div>
            
            <div class="greeting">Bonjour {{ $recipientName }},</div>
            
            <div style="color: #4a5568; font-size: 15px; margin-bottom: 25px;">
                Ceci est une confirmation concernant l'ouverture de votre nouvelle diligence (projet) au sein de notre établissement. 
            </div>

            <div class="note-title">Récapitulatif :</div>
            <div class="note-box" style="line-height: 2;">
                <span style="color: #718096;">•</span> <strong>Diligence :</strong> {{ $projectName }}<br>
                <span style="color: #718096;">•</span> <strong>Statut :</strong> Dossier ouvert / En cours d'initialisation<br>
            </div>

            @if(isset($attachments) && count($attachments) > 0)
                <div class="attachments-section">
                    <div class="attachments-title">Pièces jointes incluses :</div>
                    <ul class="attachments-list">
                        @foreach($attachments as $attachment)
                            <li class="attachment-item">
                                <span class="attachment-bullet">•</span>
                                <a href="{{ $attachment['url'] }}" class="attachment-link">{{ $attachment['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="footer-text">
                Nous sommes ravis de vous accompagner dans cette démarche. Vous recevrez des notifications en temps réel au fur et à mesure de l'avancement de votre dossier.
                <br><br>
                Notre équipe reste à votre entière disposition pour tout complément d'information.
            </div>

            <div class="signature">
                Cordialement,<br>
                <div class="signature-name">{{ $companyName }}</div>
            </div>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Suivre mon dossier</a>
            </div>
        </div>
    </div>
</body>
</html>
