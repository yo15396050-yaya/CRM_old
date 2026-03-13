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
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 5px; 
        }
        .header-line { border-top: 1px solid #e1e4e8; width: 150px; margin: 10px auto; }
        
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
        
        .panel { 
            background-color: #f8fafc; 
            border: 1px solid #edf2f7; 
            border-radius: 10px; 
            padding: 24px; 
            color: #4a5568; 
            font-size: 15px; 
            margin-bottom: 30px; 
            line-height: 1.8;
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
        
        .footer-text { margin-top: 40px; color: #718096; font-size: 14px; }
        .signature { margin-top: 35px; font-size: 15px; color: #2d3748; }
        .signature-name { 
            font-weight: 700; 
            font-size: 18px; 
            margin-top: 8px; 
            color: {{ $company->header_color }}; 
            text-transform: uppercase; 
        }
    </style>
</head>
<body>
    <div class="header-top">
        <div class="company-name">{{ $company->company_name }}</div>
        <div class="header-line"></div>
    </div>

    <div class="main-card">
        <div class="card-content">
            <div class="objet">Message de {{ $userChat->fromUser->name }}</div>
            
            <div class="greeting">Bonjour {{ $recipient->name }},</div>
            
            <div style="color: #4a5568; font-size: 15px; margin-bottom: 25px;">
                Vous avez reçu une nouvelle communication concernant votre dossier :
            </div>

            <div class="panel">
                {!! $userChat->message !!}
            </div>

            @if(count($attachments) > 0)
            <div class="attachments-section">
                <div class="attachments-title">
                    📎 Pièces jointes au message :
                </div>
                <ul class="attachments-list">
                    @foreach($attachments as $file)
                    <li class="attachment-item">
                        <span class="attachment-bullet">•</span>
                        <a href="{{ $file['url'] }}" class="attachment-link">{{ $file['name'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Répondre au message</a>
            </div>

            <div class="footer-text">
                Merci de nous contacter si vous avez des questions ou besoin d'assistance.
            </div>

            <div class="signature">
                Cordialement,<br>
                L'équipe de production<br>
                <div class="signature-name">{{ $company->company_name }}</div>
            </div>
        </div>
    </div>
</body>
</html>
