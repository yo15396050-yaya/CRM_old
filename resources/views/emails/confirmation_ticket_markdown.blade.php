@component('mail::message')
# Confirmation d'inscription

Bonjour {{ $ticketData['nom_complet'] }},

Votre inscription à la formation sur **les bénéficiaires effectifs et le blanchiment de capitaux** a bien été enregistrée.

**Détails de votre réservation:**
- **N° Ticket:** {{ $ticketData['ticket_number'] }}
- **Date:** Samedi 30 Août 2025
- **Heure:** 9h - 12h
- **Lieu:** Cocody, Abidjan
- **Nombre de places:** {{ $ticketData['nombre_tickets'] }}
- **Montant payé:** {{ $ticketData['montant_total'] }}

Vous trouverez ci-joint votre ticket de confirmation à imprimer et présenter le jour J.

@component('mail::button', ['url' => 'https://dc-knowing.com/CRM/public/form'])
Voir les informations pratiques
@endcomponent

Pour toute question, contactez-nous via notre adresse mail dcknowing@gmail.com ou au +225 27 22 42 14 43.

Cordialement,  
L'équipe de formation
@endcomponent