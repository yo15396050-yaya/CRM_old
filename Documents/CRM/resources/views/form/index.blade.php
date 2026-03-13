<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileImage" content="{{ isset($company)?$company->favicon_url:global_setting()->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ isset($company)?$company->favicon_url:global_setting()->favicon_url }}">

    <title>Inscription - C'est déjà Noel à DC-Knowing</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
       body {
            font-family: 'Public Sans', sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: white;
        }

        h2 {
            color: #000;
            padding: 20px 0;
            margin: 0;
            background-color: #ffcc00;
            text-align: center;
            font-size: 1.5rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0;
            min-height: calc(100vh - 80px);
        }

        .banniere {
            height: 100vh;
            position: sticky;
            top: 80px;
            overflow: hidden;
        }

        .banniere img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .form-container {
            background-color: white;
            padding: 20px;
            overflow-y: auto;
            height: 100%;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            width: 100%;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #ffcc00;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 204, 0, 0.2);
        }

        .btn-primary {
            background-color: #000;
            color: #ffcc00;
            border: none;
            padding: 15px 30px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #333;
            color: #fff;
        }

        .btn-primary:disabled {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        label {
            font-weight: 600;
            color: #000;
            margin-bottom: 8px;
            display: block;
        }

        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .price-display {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ffcc00;
            margin: 15px 0;
            text-align: center;
        }

        .price-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
        }

        .price-per-ticket {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .footer-message {
            background-color: #000;
            color: #ffcc00;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            border-radius: 0 0 8px 8px;
        }

        .info-highlight {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #ffcc00;
            margin: 15px 0;
        }

        .participant-type-container {
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .participant-type-option {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            border: 2px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .participant-type-option:hover {
            background-color: #e9ecef;
        }

        .participant-type-option.selected {
            background-color: #fff3cd;
            border-color: #ffcc00;
        }

        .participant-type-option input[type="radio"] {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .participant-type-label {
            flex: 1;
            font-weight: 600;
            color: #333;
        }

        .participant-type-price {
            font-weight: bold;
            color: #000;
        }

        .participant-type-details {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }

        /* Nouveaux styles pour les champs mixtes */
        .mixte-fields {
            background-color: #fff3cd;
            border: 2px solid #ffcc00;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            display: none;
        }

        .mixte-fields.show {
            display: block;
        }

        .mixte-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            align-items: end;
        }

        .mixte-field {
            flex: 1;
        }

        .mixte-field label {
            font-size: 0.9rem;
            color: #856404;
            margin-bottom: 5px;
        }

        .mixte-field input {
            border: 2px solid #ffcc00;
            background-color: white;
        }

        .mixte-summary {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #666;
        }

        .hidden {
            display: none !important;
        }

        /* Styles pour les formations avec cases à cocher */
        .formation-option {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 12px;
            border: 2px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .formation-option:hover {
            background-color: #e9ecef;
        }

        .formation-option.selected {
            background-color: #fff3cd;
            border-color: #ffcc00;
        }

        .formation-option input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.2);
            cursor: pointer;
        }

        .formation-price {
            font-weight: bold;
            color: #000;
            min-width: 150px;
            text-align: right;
        }

        .formation-tickets-item {
            background-color: white;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .formation-tickets-item label {
            margin: 0;
            font-weight: 600;
            color: #000;
            flex: 1;
        }

        .formation-tickets-item input {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .formation-tickets-item .formation-subtotal {
            min-width: 150px;
            text-align: right;
            font-weight: 600;
            color: #000;
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                min-height: auto;
            }
            
            .banniere {
                height: 300px;
                position: relative;
                top: 0;
            }
            
            .form-container {
                height: auto;
                overflow-y: visible;
            }

            h2 {
                position: relative;
            }

            .mixte-row {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="col-md-10 mx-auto">
            <h2 class="mb-0">
                📚 C'est déjà Noel à DC-Knowing !!! <br> <strong> Le cabinet DC-Knowing vous offre un cocktail de formation !</strong>
            </h2>
    
            <div class="row">  
                <div class="col-md-6 p-3">
                    <div class="banniere">
                        <img src="{{ asset('img/New_formatio.jpeg') }}" alt="Bannière de formation">
                    </div>
                </div>
                
                <div class="col-md-6 p-0">
                    <div class="form-container">
                        <!-- Informations sur la formation -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3>🎯 À propos de cette formation</h3>
                                <p>Convaincu que la formation demeure un pilier important de la performance, <strong>le cabinet DC-Knowing</strong> vous offre un cocktail de formation pour aborder 2026 en toute sérénité avec des experts de renom.</p>                                
                                <div class="info-highlight">
                                    <h4>📅 Informations pratiques :</h4>
                                    <ul>
                                        <li><strong>📅 Date :</strong> Du 11 au 18 Décembre 2025</li>
                                        <li><strong>🕐 Horaire :</strong> 9h00 - 12h00 & 15h00 - 18h00</li>
                                        <li><strong>📍 Lieu :</strong> Cocody, Abidjan</li>
                                        <li><strong>💰 Tarif :</strong> 10 000 FCFA </li>
                                        <li><strong>🎓 Certificat :</strong> Remis en fin de formation</li>
                                    </ul>
                                </div>
                                
                                <p><strong>📞 Contact :</strong> +225 27 22 42 14 43 ou +225 07 67 13 19 93</p>
                                <p><strong>⚠️ Important :</strong> Places limitées ! Seules les inscriptions avec paiement confirmé sont validées.</p> 
                            </div>
                        </div>
                             
                        <!-- Formulaire d'inscription -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3>📝 Formulaire d'inscription</h3>
                                
                                <form action="{{ route('form.store') }}" method="POST" id="inscriptionForm">
                                    @csrf
                                    
                                    <input type="hidden" name="name_form" value="COCKTAIL DE FORMATION CHEZ DC-KNOWING !">

                                    <!-- Sélection de la formation (Plusieurs formations possibles) -->
                                    <div class="form-group">
                                        <label>🎓 Choisissez une ou plusieurs formations :</label>
                                        <div class="participant-type-container">
                                            <div class="formation-option" onclick="toggleFormation(this, 'btp')">
                                                <input type="checkbox" name="formations[]" value="BTP - Réussir la planification d'un chantier" id="formation_btp" class="formation-checkbox" onchange="onFormationChange()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">🏗️ BTP</div>
                                                    <div class="participant-type-details">Réussir la planification d'un chantier en garantissant la qualité et la marge</div>
                                                    <div class="participant-type-details" style="color: #000; font-weight: 600; margin-top: 4px;">📅 Jeudi 18 décembre | ⏰ 9h - 12h</div>
                                                </div>
                                                <div class="formation-price" data-formation-id="btp">10 000 FCFA</div>
                                            </div>
                                            
                                            <div class="formation-option" onclick="toggleFormation(this, 'ia')">
                                                <input type="checkbox" name="formations[]" value="Informatique/IA - L'IA au service de la performance" id="formation_ia" class="formation-checkbox" onchange="onFormationChange()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">💻 Informatique / IA</div>
                                                    <div class="participant-type-details">L'IA au service de la performance des entreprises et des salariés</div>
                                                    <div class="participant-type-details" style="color: #000; font-weight: 600; margin-top: 4px;">📅 Mardi 16 décembre | ⏰ 9h - 12h</div>
                                                </div>
                                                <div class="formation-price" data-formation-id="ia">10 000 FCFA</div>
                                            </div>
                                            
                                            <div class="formation-option" onclick="toggleFormation(this, 'gestion')">
                                                <input type="checkbox" name="formations[]" value="Gestion - FNE pratique et conseils fiscaux" id="formation_gestion" class="formation-checkbox" onchange="onFormationChange()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">📊 Gestion</div>
                                                    <div class="participant-type-details">FNE - pratique et conseils pour éviter les risques fiscaux</div>
                                                    <div class="participant-type-details" style="color: #000; font-weight: 600; margin-top: 4px;">📅 Jeudi 11 décembre | ⏰ 9h - 12h</div>
                                                </div>
                                                <div class="formation-price" data-formation-id="gestion">10 000 FCFA</div>
                                            </div>
                                            
                                            <div class="formation-option" onclick="toggleFormation(this, 'rh')">
                                                <input type="checkbox" name="formations[]" value="Ressources Humaines - Digitalisation des processus RH" id="formation_rh" class="formation-checkbox" onchange="onFormationChange()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">👥 Ressources Humaines</div>
                                                    <div class="participant-type-details">Digitalisation des processus RH pour une meilleure efficacité du personnel</div>
                                                    <div class="participant-type-details" style="color: #000; font-weight: 600; margin-top: 4px;">📅 Jeudi 11 décembre | ⏰ 15h - 18h</div>
                                                </div>
                                                <div class="formation-price" data-formation-id="rh">10 000 FCFA</div>
                                            </div>
                                            
                                            <div class="formation-option" onclick="toggleFormation(this, 'transport')">
                                                <input type="checkbox" name="formations[]" value="Transport & Logistique - Nouveau code douanier" id="formation_transport" class="formation-checkbox" onchange="onFormationChange()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">🚛 Transport & Logistique</div>
                                                    <div class="participant-type-details">Nouveau code douanier : se mettre à niveau pour éviter les pièges</div>
                                                    <div class="participant-type-details" style="color: #000; font-weight: 600; margin-top: 4px;">📅 Jeudi 18 décembre | ⏰ 15h - 18h</div>
                                                </div>
                                                <div class="formation-price" data-formation-id="transport">10 000 FCFA</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sélection du nombre de tickets par formation -->
                                    <div class="mb-4" id="formationsTicketsContainer" style="display: none;">
                                        <h4 style="margin: 20px 0 15px 0; color: #000;">🎫 Nombre de places par formation</h4>
                                        <div id="formationsTicketsList" style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; border: 2px solid #dee2e6;">
                                            <!-- Les champs de tickets seront générés dynamiquement ici -->
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nom">👤 Nom & Prénoms ou Nom Entreprise</label>
                                        <input type="text" class="form-control" id="nom" name="nom" 
                                               required placeholder="Votre nom complet">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">📧 Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="votre.email@exemple.com">
                                    </div>

                                    <div class="form-group">
                                        <label for="numero">📱 Numéro téléphone (WhatsApp de préférence)</label>
                                        <input type="tel" class="form-control" id="numero" name="numero" 
                                               placeholder="+225 XX XX XX XX XX">
                                    </div>

                                    <!-- Options de type de participant -->
                                    <div class="form-group" style="display: none;">
                                        <label>👥 Type de participation :</label>
                                        <div class="participant-type-container">
                                            <div class="participant-type-option selected" onclick="selectParticipantType('membre')">
                                                <input type="radio" name="participant_type" value="membre" id="type_membre" checked onchange="calculateTotal()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">🟢 Membre uniquement</div>
                                                    <div class="participant-type-details">Tarif préférentiel pour les membres</div>
                                                </div>
                                                <div class="participant-type-price">10 000 FCFA/place</div>
                                            </div>
                                            
                                            <div class="participant-type-option" onclick="selectParticipantType('non_membre')">
                                                <input type="radio" name="participant_type" value="non_membre" id="type_non_membre" onchange="calculateTotal()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">🔵 Non-membre uniquement</div>
                                                    <div class="participant-type-details">Tarif standard pour les non-membres</div>
                                                </div>
                                                <div class="participant-type-price">10 000 FCFA/place</div>
                                            </div>
                                            
                                            <div class="participant-type-option" onclick="selectParticipantType('mixte')">
                                                <input type="radio" name="participant_type" value="mixte" id="type_mixte" onchange="calculateTotal()">
                                                <div style="flex: 1;">
                                                    <div class="participant-type-label">🟡 Les deux (Membre + Non-membre)</div>
                                                    <div class="participant-type-details">Inscription mixte : définissez le nombre pour chaque type</div>
                                                </div>
                                                <div class="participant-type-price">Calculé automatiquement</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champs pour l'option mixte -->
                                    <div class="mixte-fields" id="mixteFields">
                                        <h4>🎯 Définissez le nombre de places par type :</h4>
                                        <div class="mixte-row">
                                            <div class="mixte-field">
                                                <label for="membre_places">🟢 Nombre de places MEMBRE</label>
                                                <input type="number" class="form-control" id="membre_places" 
                                                       name="membre_places" min="0" max="10" value="1" 
                                                       onchange="calculateMixteTotal()">
                                            </div>
                                            <div class="mixte-field">
                                                <label for="non_membre_places">🔵 Nombre de places NON-MEMBRE</label>
                                                <input type="number" class="form-control" id="non_membre_places" 
                                                       name="non_membre_places" min="0" max="10" value="1" 
                                                       onchange="calculateMixteTotal()">
                                            </div>
                                        </div>
                                        <div class="mixte-summary" id="mixteSummary">
                                            Total : 2 places (1 membre + 1 non-membre)
                                        </div>
                                    </div>

                                    <!-- Nombre de places pour les options simples -->
                                    <div class="form-group" id="simpleTicketsGroup" style="display: none;">
                                        <label for="tickets">🎫 Nombre de places *</label>
                                        <input type="number" class="form-control" id="tickets" name="tickets" 
                                               value="1" min="1" max="10" required onchange="calculateTotal()">
                                        <small class="text-muted" id="ticketHint">Maximum 10 places par inscription</small>
                                    </div>

                                    <!-- Affichage du prix -->
                                    <div class="price-display" id="priceDisplay">
                                        <div class="price-amount" id="totalAmount">10 000 FCFA</div>
                                        <div class="price-per-ticket" id="priceBreakdown">10 000 FCFA × 1 place(s)</div>
                                    </div>

                                    <!-- Champs cachés pour les données -->
                                    <input type="hidden" id="paiement" name="paiement" value="10000">
                                    <input type="hidden" id="membre_count" name="membre_count" value="1">
                                    <input type="hidden" id="non_membre_count" name="non_membre_count" value="0">
                                    <input type="hidden" id="total_tickets" name="total_tickets" value="1">

                                    <div class="form-group" id="nomdiplome">
                                    </div>

                                    <div class="form-group">
                                        <label for="commentaire">💬 Commentaire ou question supplémentaire</label>
                                        <textarea id="commentaire" name="commentaire" rows="4" class="form-control"
                                                  placeholder="Vos questions, besoins spécifiques, régime alimentaire..."></textarea>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong>📄 Processus d'inscription :</strong><br>
                                        1️⃣ Remplissez ce formulaire<br>
                                        2️⃣ Procédez au paiement via notre lien de paiement ou QR code<br>
                                        3️⃣ Confirmez votre paiement<br>
                                        4️⃣ Recevez votre ticket par email
                                    </div>

                                    <div class="form-group text-center">
                                        <button type="submit" class="btn-primary" id="submitBtn">
                                            🚀 Procéder à l'inscription et au paiement
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Informations de contact -->
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h4>🤝 Besoin d'aide ?</h4>
                                    <p>Notre équipe est là pour vous accompagner dans votre inscription.</p>
                                    <p><strong>📧 Email :</strong> <a href="mailto:dcknowing@gmail.com">dcknowing@gmail.com</a></p>
                                    <p><strong>📞 Téléphone :</strong> +225 27 22 42 14 43</p>
                                    <p><strong>💬 WhatsApp :</strong> +225 07 67 13 19 93</p>
                                </div>
                            </div>
                            <div class="footer-message">
                                ✨ À très bientôt pour cette formation enrichissante !<br>
                                <strong>L'équipe DC-KNOWING</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Configuration des formations
        const formations = {
            'btp': { name: '🏗️ BTP', price: 10000, label: 'Jeudi 18 décembre | 9h - 12h' },
            'ia': { name: '💻 Informatique / IA', price: 10000, label: 'Mardi 16 décembre | 9h - 12h' },
            'gestion': { name: '📊 Gestion', price: 10000, label: 'Jeudi 11 décembre | 9h - 12h' },
            'rh': { name: '👥 Ressources Humaines', price: 10000, label: 'Jeudi 11 décembre | 15h - 18h' },
            'transport': { name: '🚛 Transport & Logistique', price: 10000, label: 'Jeudi 18 décembre | 15h - 18h' }
        };

        // Sélection des formations avec checkbox
        function toggleFormation(element, formationId) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            onFormationChange();
        }

        // Gestion du changement de formation
        function onFormationChange() {
            const selectedFormations = [];
            const checkboxes = document.querySelectorAll('.formation-checkbox:checked');

            // Collecter les formations sélectionnées
            checkboxes.forEach(checkbox => {
                const formationId = checkbox.id.replace('formation_', '');
                selectedFormations.push(formationId);
                
                // Ajouter la classe selected à l'élément parent
                const option = checkbox.closest('.formation-option');
                if (option) option.classList.add('selected');
            });

            // Retirer la classe selected des formations non sélectionnées
            document.querySelectorAll('.formation-checkbox:not(:checked)').forEach(checkbox => {
                const option = checkbox.closest('.formation-option');
                if (option) option.classList.remove('selected');
            });

            // Afficher/masquer le conteneur de tickets
            const container = document.getElementById('formationsTicketsContainer');
            if (selectedFormations.length > 0) {
                container.style.display = 'block';
                generateFormationTicketsFields(selectedFormations);
            } else {
                container.style.display = 'none';
            }

            calculateTotal();
        }

        // Générer les champs de nombre de tickets pour les formations sélectionnées
        function generateFormationTicketsFields(selectedFormations) {
            const ticketsList = document.getElementById('formationsTicketsList');
            ticketsList.innerHTML = '';

            selectedFormations.forEach(formationId => {
                const formation = formations[formationId];
                const div = document.createElement('div');
                div.className = 'formation-tickets-item';
                div.dataset.formationId = formationId;
                
                div.innerHTML = `
                    <label for="tickets_${formationId}">${formation.name}</label>
                    <input type="number" id="tickets_${formationId}" name="formation_tickets[${formationId}]" 
                           value="1" min="1" max="10" onchange="calculateTotal()">
                    <div class="formation-subtotal" id="subtotal_${formationId}">10 000 FCFA</div>
                `;
                
                ticketsList.appendChild(div);
            });
        }

        // Calcul automatique du prix total
        function calculateTotal() {
            const checkboxes = document.querySelectorAll('.formation-checkbox:checked');
            
            if (checkboxes.length === 0) {
                document.getElementById('priceDisplay').style.display = 'none';
                document.getElementById('paiement').value = 0;
                return;
            }

            let grandTotal = 0;
            let breakdown = [];
            let totalTickets = 0;

            checkboxes.forEach(checkbox => {
                const formationId = checkbox.id.replace('formation_', '');
                const ticketsInput = document.getElementById(`tickets_${formationId}`);
                const tickets = parseInt(ticketsInput.value) || 1;
                
                // Validation
                if (tickets < 1) {
                    ticketsInput.value = 1;
                    return calculateTotal();
                }
                if (tickets > 10) {
                    ticketsInput.value = 10;
                    return calculateTotal();
                }

                const formation = formations[formationId];
                const subtotal = tickets * formation.price;
                
                grandTotal += subtotal;
                totalTickets += tickets;
                breakdown.push(`${tickets} × ${formation.name.split(' ')[0]} (${subtotal.toLocaleString('fr-FR')} FCFA)`);

                // Mettre à jour le sous-total
                document.getElementById(`subtotal_${formationId}`).textContent = 
                    subtotal.toLocaleString('fr-FR') + ' FCFA';
            });

            // Validation du total
            if (totalTickets > 10) {
                alert('⚠️ Le nombre total de places ne peut pas dépasser 10');
                // Réduire les tickets
                let excess = totalTickets - 10;
                checkboxes.forEach(checkbox => {
                    if (excess > 0) {
                        const formationId = checkbox.id.replace('formation_', '');
                        const ticketsInput = document.getElementById(`tickets_${formationId}`);
                        const reduction = Math.min(excess, parseInt(ticketsInput.value) - 1);
                        if (reduction > 0) {
                            ticketsInput.value = parseInt(ticketsInput.value) - reduction;
                            excess -= reduction;
                        }
                    }
                });
                return calculateTotal();
            }

            // Afficher le prix
            document.getElementById('priceDisplay').style.display = 'block';
            document.getElementById('totalAmount').textContent = grandTotal.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('priceBreakdown').textContent = breakdown.join(' + ');
            document.getElementById('paiement').value = grandTotal;
            document.getElementById('total_tickets').value = totalTickets;

            // Générer les champs de noms pour les participants
            generateParticipantFields(totalTickets);
        }

        // Générer les champs de nom pour chaque participant
        function generateParticipantFields(totalTickets) {
            const nomdiplomeDiv = document.getElementById('nomdiplome');
            nomdiplomeDiv.innerHTML = '';

            for (let i = 1; i <= totalTickets; i++) {
                const div = document.createElement('div');
                div.classList.add("nomdiplome-field");
                div.innerHTML = `
                    <label for="nomdiplome_${i}">👤 Nom pour le certificat - Participant ${i}</label>
                    <input type="text" name="nomdiplome[${i}]" placeholder="Nom comme souhaité sur le certificat" class="form-control mb-4" required />
                `;
                nomdiplomeDiv.appendChild(div);
            }
        }

        // Validation du formulaire
        document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const checkboxes = document.querySelectorAll('.formation-checkbox:checked');
            
            if (!nom) {
                alert('⚠️ Veuillez saisir votre nom complet');
                document.getElementById('nom').focus();
                return;
            }

            if (checkboxes.length === 0) {
                alert('⚠️ Veuillez sélectionner au moins une formation');
                return;
            }
            
            let totalTickets = 0;
            checkboxes.forEach(checkbox => {
                const formationId = checkbox.id.replace('formation_', '');
                const tickets = parseInt(document.getElementById(`tickets_${formationId}`).value) || 0;
                totalTickets += tickets;
            });

            if (totalTickets === 0) {
                alert('⚠️ Veuillez sélectionner au moins 1 place pour une formation');
                return;
            }

            if (totalTickets > 10) {
                alert('⚠️ Maximum 10 places au total');
                return;
            }
            
            // Vérifier que tous les noms de certificats sont remplis
            const nomInputs = document.querySelectorAll('input[name^="nomdiplome"]');
            for (let input of nomInputs) {
                if (!input.value.trim()) {
                    alert('⚠️ Veuillez remplir tous les noms pour les certificats');
                    input.focus();
                    return;
                }
            }
            
            // Désactiver le bouton de soumission pour éviter les doublons
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Traitement en cours...';
        });

        // Auto-formatage du numéro de téléphone
        document.getElementById('numero').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('225')) {
                value = '+' + value;
            } else if (value.length >= 8 && !value.startsWith('225')) {
                value = '+225 ' + value;
            }
            e.target.value = value;
        });

        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            // Le calcul se fera quand une formation sera sélectionnée
        });
    </script>
</body>
</html>