Bonjour {{ $recipientName }},

Vous intervenez sur le dossier : {{ $projectName }}
@if(!empty($clientName))
Ouvert pour le compte de {{ $clientName }}.
@endif

@if(!empty($projectCode))
Reference          : {{ $projectCode }}
@endif
@if(!empty($natureName))
Nature             : {{ $natureName }}
@endif
@if(!empty($periodLabel))
Periode couverte   : {{ $periodLabel }}
@endif
@if(!empty($deadline))
Echeance du dossier: {{ $deadline }}
@endif
@if(!empty($adminName))
Responsable        : {{ $adminName }}
@endif
Affectation        : {{ $date }}
@if(!empty($teamMembers))

EQUIPE AFFECTEE
@foreach($teamMembers as $member)
- {{ $member }}
@endforeach
@endif
@if(!empty($summary))

{{ strip_tags($summary) }}
@endif
@if(!empty($attachments))

PIECES DU DOSSIER
@foreach($attachments as $attachment)
- {{ $attachment['name'] }} : {{ $attachment['url'] }}
@endforeach
@endif

Acceder au dossier : {{ $url }}

--
Affectation interne {{ $companyName }}. Les informations de ce dossier sont couvertes par le secret professionnel.
