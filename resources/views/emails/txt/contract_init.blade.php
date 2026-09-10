Bonjour {{ $recipientName }},

Votre contrat est pret. Vous pouvez le lire integralement puis le signer en
ligne, sans rien imprimer ni renvoyer.

{{ $subject }}

Reference        : {{ $contractNumber }}
Type de contrat  : {{ $contractType }}
Etabli le        : {{ $date }}
A signer avant le: {{ $expiryDate }}

Lire et signer le contrat : {{ $url }}

Ce lien vous est personnel et reste valable {{ $expiryDays }} jours. Ne le
transferez pas : toute signature apposee depuis ce lien vous sera attribuee.
Passe ce delai, demandez-nous simplement un nouvel envoi.

Une question sur une clause avant de signer ? Repondez a cet e-mail.

--
{{ $companyName }}@if(!empty($company->company_email)) · {{ $company->company_email }}@endif
