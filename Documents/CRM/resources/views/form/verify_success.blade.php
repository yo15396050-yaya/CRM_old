<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket Valide</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e8f5e9; text-align: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: inline-block; }
        h1 { color: #2e7d32; }
        p { font-size: 16px; }
        .info { margin-top: 20px; text-align: left; }
    </style>
</head>
<body>
    <div class="card">
        <h1>✅ Ticket Valide</h1>
        <p>Le ticket <strong>{{ $ticket->ticket_number }}</strong> est valide.</p>

        <div class="info">
            <p><strong>Nom :</strong> {{ $ticket->nom_complet }}</p>
            <p><strong>Email :</strong> {{ $ticket->email }}</p>
            <p><strong>Téléphone :</strong> {{ $ticket->telephone }}</p>
            <p><strong>Nombre de tickets :</strong> {{ $ticket->nombre_tickets }}</p>
        </div>
    </div>
</body>
</html>
