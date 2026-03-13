<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
 
    <title>Formulaire d'Inscription</title>
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/simple-line-icons.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel='stylesheet' href="{{ asset('vendor/css/dragula.css') }}" type='text/css' />
    <link rel='stylesheet' href="{{ asset('vendor/css/drag.css') }}" type='text/css' />

    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" href="{{ isset($company) ? $company->favicon_url : global_setting()->favicon_url }}">

    @include('sections.theme_css')
    @stack('styles')

    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        h2 {
            color: #000;
            padding: 20px 0;
            margin: 0;
            background-color: #ffcc00; /* Jaune */
            text-align: center;
            font-size: 1.5rem;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0;
            height: calc(100vh - 80px);
        }

        .banniere {
            height: 100vh;
            position: sticky;
            top: 0;
            overflow: hidden;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.1);
        }

        .banniere img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            border: none;
        }

        .card-header {
            background-color: #ffcc00;
            color: #000;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #000;
            color: #ffcc00;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #333;
            color: #fff;
        }

        .card-body {
            padding: 30px;
        }

        .formation-summary {
            background-color: #fff3cd;
            border-left: 4px solid #ffcc00;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .formation-item {
            background-color: white;
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .formation-name {
            font-weight: 600;
            color: #000;
            flex: 1;
        }

        .formation-places {
            display: inline-block;
            background-color: #ffcc00;
            color: #000;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-left: 10px;
        }

        .formation-date {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }

        .tickets-list {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .ticket-item {
            background-color: white;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 4px solid #ffcc00;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                height: auto;
            }
        }
    </style>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
</head>
<body>
    <div class="content-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header">✅ Inscription réussie</div>
    
                        <div class="card-body">
                            <div class="alert alert-success">
                                <strong>🎉 Félicitations !</strong> Votre inscription a été enregistrée avec succès et votre paiement a été confirmé.
                            </div>

                            <!-- Résumé des formations -->
                            @php
                                $formations = json_decode($registration->label_formation, true);
                                $isArray = is_array($formations) && isset($formations[0]['nom']);
                            @endphp

                            @if($isArray && count($formations) > 0)
                                <div class="formation-summary">
                                    <h5 style="margin-top: 0; color: #856404;">📚 Formations pour lesquelles vous êtes inscrit :</h5>
                                    @foreach($formations as $formation)
                                        <div class="formation-item">
                                            <div>
                                                <div class="formation-name">{{ $formation['nom'] ?? 'Formation' }}</div>
                                                <div class="formation-date">📅 {{ $formation['date'] ?? 'À confirmer' }}</div>
                                            </div>
                                            <span class="formation-places">{{ $formation['nombre_places'] ?? 1 }} place{{ ($formation['nombre_places'] ?? 1) > 1 ? 's' : '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="formation-summary">
                                    <h5 style="margin-top: 0; color: #856404;">📚 Formation :</h5>
                                    <p>{{ $registration->label_formation }}</p>
                                </div>
                            @endif

                            <p><strong>🎫 Nombre de tickets générés : {{ count($tickets) }}</strong></p>
    
                            @if(count($tickets) > 0)
                                <div class="tickets-list">
                                    <h5>📋 Vos tickets :</h5>
                                    <ul class="list-group" style="list-style: none; padding: 0; margin: 0;">
                                        @foreach($tickets as $ticket)
                                            <li class="ticket-item">
                                                <div>
                                                    <strong>🎫 {{ $ticket->participant_name }}</strong>
                                                    <div style="font-size: 0.85rem; color: #666; margin-top: 4px;">
                                                        Ticket #{{ substr($ticket->ticket_number, -8) }}
                                                    </div>
                                                </div>
                                                <a href="{{ route('form.download', ['ticket_number' => $ticket->ticket_number]) }}" class="btn btn-sm btn-primary">
                                                    📥 Télécharger
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
    
                                <div class="text-center mt-4">
                                    <a href="{{ route('form.downloadAll', ['registration' => $registration->id]) }}" class="btn btn-success" style="background-color: #28a745;">
                                        📦 Télécharger tous les tickets (ZIP)
                                    </a>
                                </div>
                            @endif
    
                            <div class="mt-4 text-center mb-4" style="background-color: #f0f0f0; padding: 15px; border-radius: 4px;">
                                <p style="margin: 5px 0;"><strong>📧 Confirmation envoyée</strong></p>
                                <p style="margin: 5px 0; font-size: 0.9rem;">Un email de confirmation a été envoyé à votre adresse avec vos tickets en pièce jointe.</p>
                                <p style="margin: 5px 0; font-size: 0.9rem;"><strong>📞 Support :</strong> +225 27 22 42 14 43 ou +225 07 67 13 19 93</p>
                            </div>
    
                            <div class="text-center">
                                <a href="{{ route('form.index') }}" class="btn btn-primary">
                                    ← Retour au formulaire
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>