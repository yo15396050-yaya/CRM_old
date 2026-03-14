<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouveau Contrat DC-KNOWING</title>
<style>
  body {
    font-family: 'Arial', sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
  }
  .container {
    max-width: 600px;
    margin: 50px auto;
    background-color: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .header {
    background-color: #ffffff;
    text-align: center;
    padding: 20px;
  }
  .header img {
    max-height: 100px;
  }
  .content {
    padding: 30px;
    text-align: center;
    color: #333333;
  }
  .content h2 {
    color: #000000; /* noir */
    margin-top: 0;
  }
  .content p {
    line-height: 1.6;
    margin: 10px 0;
  }
  .contract-details {
    background-color: #f9f9f9;
    padding: 15px;
    margin: 20px 0;
    border-radius: 8px;
    text-align: left;
  }
  .contract-details p {
    margin: 5px 0;
  }
  .btn {
    display: inline-block;
    padding: 12px 25px;
    background-color: #000000; /* noir */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 20px;
  }
  .footer {
    background-color: #f0f2f5;
    font-size: 12px;
    color: #888888;
    text-align: center;
    padding: 15px;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{ $logo }}" alt="Logo DC-KNOWING">
    </div>
    <div class="content">
      <h2>Bonjour {{ $notifiableName }} !</h2>
      
      @if(str_contains(strtolower($contractType), 'nouvelle') || str_contains(strtolower($contractType), 'adhésion'))
        <p>Nous avons le plaisir de vous informer que votre contrat de <strong>nouvelle adhésion</strong> DC-KNOWING est désormais disponible. Vous pouvez le consulter et le signer en toute sécurité via le lien ci-dessous.</p>
      @elseif(str_contains(strtolower($contractType), 'renouvellement'))
        <p>Nous vous informons que votre contrat de <strong>renouvellement</strong> DC-KNOWING a été généré. Nous vous invitons à le consulter et à le confirmer via le lien ci-dessous.</p>
      @else
        <p>Nous avons le plaisir de vous informer que votre contrat DC-KNOWING est désormais disponible. Vous pouvez le consulter et le signer en toute sécurité via le lien ci-dessous.</p>
      @endif

      <div class="contract-details">
        <p><strong>Type de contrat :</strong> {{ $contractType }}</p>
        <p><strong>Date de création :</strong> {{ $createdAt }}</p>
        <p><strong>Numéro de contrat :</strong> {{ $contractNumber }}</p>
      </div>

      <a href="{{ $url }}" class="btn">Voir le contrat</a>

      <p>Pour toute question ou assistance, contactez-nous :</p>
      <p>📧 {{ $supportEmail }} | 📞 {{ $supportPhone }}</p>
      <p>Merci pour votre confiance,<br>L’équipe <strong>DC-KNOWING</strong></p>
    </div>
    <div class="footer">
      © {{ date('Y') }} DC-KNOWING - Ce message est confidentiel.
    </div>
  </div>
</body>
</html>
