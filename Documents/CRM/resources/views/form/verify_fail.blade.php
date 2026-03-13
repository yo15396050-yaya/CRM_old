<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket Invalide</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #ffebee; text-align: center; padding: 50px; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: inline-block; }
        h1 { color: #c62828; }
        p { font-size: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>❌ Ticket Invalide</h1>
        <p>Le ticket <strong>{{ $code }}</strong> n'existe pas ou n'est pas reconnu.</p>
    </div>
</body>
</html>
