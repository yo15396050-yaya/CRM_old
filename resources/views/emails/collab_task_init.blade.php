<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Public Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #3d4852; 
            background-color: #f0f4f8; 
            margin: 0; 
            padding: 40px 20px; 
        }
        
        .header-top { text-align: center; margin-bottom: 25px; }
        .company-name { 
            color: #2d3748; 
            font-size: 20px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
        }
        .badge-internal { 
            display: inline-block; 
            background: {{ $company->header_color }}11; 
            color: {{ $company->header_color }}; 
            border: 1px solid {{ $company->header_color }}33; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 700; 
            padding: 4px 14px; 
            margin-top: 8px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        .main-card { 
            background: #fff; 
            max-width: 650px; 
            margin: 0 auto; 
            border-radius: 12px; 
            border-top: 6px solid {{ $company->header_color }}; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            overflow: hidden; 
        }
        
        .card-header { 
            background: #ffffff; 
            padding: 30px 40px; 
            border-bottom: 1px solid #edf2f7;
        }
        .action-label { 
            color: {{ $company->header_color }}; 
            font-size: 12px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 8px; 
        }
        .task-title { color: #1a202c; font-size: 20px; font-weight: 700; }
        
        .card-content { padding: 35px 40px; }
        .greeting { font-size: 16px; color: #2d3748; font-weight: 700; margin-bottom: 20px; }
        
        .intro { 
            color: #4a5568; 
            font-size: 15px; 
            margin-bottom: 30px; 
            background: #f8fafc; 
            padding: 15px 20px; 
            border-radius: 8px; 
            border-left: 3px solid {{ $company->header_color }};
        }
        
        .info-grid { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 10px; 
            padding: 10px 20px; 
            margin-bottom: 30px; 
        }
        .info-row { 
            display: flex; 
            padding: 12px 0; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 14px; 
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #718096; width: 150px; flex-shrink: 0; font-size: 13px; font-weight: 500; }
        .info-value { color: #1a202c; font-weight: 600; }
        
        .note-box { 
            background: #fffbeb; 
            border: 1px solid #fef3c7; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 30px; 
            color: #92400e; 
            font-size: 14.5px; 
        }
        .note-label { 
            font-weight: 700; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #b45309; 
            margin-bottom: 10px; 
        }
        
        .attachments-box { 
            background: #f0fff4; 
            border: 1px solid #c6f6d5; 
            border-radius: 10px; 
            padding: 20px; 
            margin-bottom: 30px; 
        }
        .attachments-label { 
            font-weight: 700; 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            color: #166534; 
            margin-bottom: 12px; 
        }
        .att-item { font-size: 14px; color: #166534; margin-bottom: 8px; }
        .att-item a { color: #15803d; font-weight: 600; text-decoration: none; }
        .att-item a:hover { text-decoration: underline; }
        
        .btn-container { text-align: center; margin: 30px 0; }
        .btn { 
            background: {{ $company->header_color }}; 
            color: #fff !important; 
            padding: 14px 40px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 700; 
            display: inline-block; 
            font-size: 15px; 
            box-shadow: 0 4px 6px {{ $company->header_color }}44;
        }
        
        .contact-box { 
            background: #f8fafc; 
            border: 1px solid #edf2f7; 
            border-radius: 10px; 
            padding: 15px 25px; 
            font-size: 13px; 
            color: #64748b; 
            margin-top: 25px; 
        }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header-top">
        <div class="company-name">{{ $companyName }}</div>
        <div class="badge-internal">🔒 Notification Interne</div>
        <div class="meta-info">Le {{ $date }} à {{ $heure }}</div>
    </div>

    <div class="main-card">
        <div class="card-header">
            <div class="action-label">⚡ Nouvelle tâche assignée</div>
            <div class="task-title">{{ $taskHeading }}</div>
        </div>
        <div class="card-content">
            <div class="greeting">Bonjour {{ $recipientName }},</div>
            <div class="intro">
                Une nouvelle mission vient de vous être confiée. Merci d'en examiner les détails ci-dessous.
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">🔖 Référence</span>
                    <span class="info-value">{{ $taskReference }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📊 Statut</span>
                    <span class="info-value">{{ $taskStatus }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🎯 Priorité</span>
                    <span class="info-value">{{ ucfirst($priority) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📅 Échéance</span>
                    <span class="info-value">{{ $dueDate }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">👤 Client</span>
                    <span class="info-value">{{ $clientName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏗️ Projet</span>
                    <span class="info-value">{{ $projectName }}</span>
                </div>
                @if($responsibleName)
                <div class="info-row">
                    <span class="info-label">👨‍💼 Responsable</span>
                    <span class="info-value">{{ $responsibleName }}</span>
                </div>
                @endif
            </div>

            @if($description)
            <div class="note-box">
                <div class="note-label">📝 Instructions du responsable</div>
                <div style="line-height: 1.6;">{!! $description !!}</div>
            </div>
            @endif



            @if($responsibleEmail)
            <div class="contact-box">
                Pour toute question, contactez <strong>{{ $responsibleName }}</strong> : <a href="mailto:{{ $responsibleEmail }}" style="color: {{ $company->header_color }}; text-decoration: none;">{{ $responsibleEmail }}</a>
            </div>
            @endif
        </div>
    </div>
    <div class="footer">{{ $companyName }} — Portails Collaborateurs</div>
</body>
</html>
