Bonjour {{ $recipientName }},

Le statut de la tache « {{ $taskHeading }} » vient de changer.
@if(!empty($previousStatus) && $previousStatus !== $taskStatus)

{{ $previousStatus }} -> {{ $taskStatus }}
@else

Statut : {{ $taskStatus }}
@endif
@if(!empty($noteContent))

NOTE DE MISE A JOUR
{{ strip_tags($noteContent) }}
-- {{ $modifiedBy }}, aujourd'hui a {{ $heure }}
@endif

Client   : {{ $clientName }}
@if(!empty($projectName))
Dossier  : {{ $projectName }}
@endif
Echeance : {{ $dueDate }}@if(!empty($dueLabel)) ({{ $dueLabel }})@endif

Voir la tache : {{ $url }}

--
Vous recevez ce message car vous etes affecte a cette tache. {{ $companyName }}
