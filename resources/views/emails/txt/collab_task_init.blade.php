Bonjour {{ $recipientName }},

Cette tache vous est confiee : {{ $taskHeading }}
@if(!empty($dueLabel))
{{ $dueLabel }}
@endif

@if(!empty($taskReference))
Reference    : {{ $taskReference }}
@endif
Client       : {{ $clientName }}
@if(!empty($projectName))
Dossier      : {{ $projectName }}
@endif
@if(!empty($responsibleName))
Responsable  : {{ $responsibleName }}
@endif
Priorite     : {{ $priority }}
Echeance     : {{ $dueDate }}
Statut       : {{ $taskStatus }}
@if(!empty($description))

INSTRUCTIONS
{{ strip_tags($description) }}
@endif
@if(!empty($attachments))

PIECES DU DOSSIER
@foreach($attachments as $attachment)
- {{ $attachment['name'] }} : {{ $attachment['url'] }}
@endforeach
@endif

Ouvrir la tache : {{ $url }}

--
Notification interne {{ $companyName }}. Repondez a ce message pour joindre le responsable du dossier.
