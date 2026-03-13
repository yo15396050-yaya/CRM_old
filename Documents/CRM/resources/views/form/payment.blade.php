<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inscription - C'est déjà Noel à DC-Knowing</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background-color: #ffcc00;
            color: #000;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .payment-card {
            background-color: #fff;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .payment-option {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option:hover, .payment-option.selected {
            border-color: #ffcc00;
            background-color: #fffbf0;
        }

        .payment-option h3 {
            margin: 0 0 10px 0;
            color: #000;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            border: 2px dashed #ffcc00;
            border-radius: 8px;
            background-color: #fffbf0;
            display: none;
        }

        .qr-section.active {
            display: block;
        }

        .qr-code {
            width: 250px;
            height: 250px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #333;
        }

        .formation-list {
            background-color: #fff3cd;
            border-left: 4px solid #ffcc00;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        .formation-item {
            padding: 10px 0;
            border-bottom: 1px solid #ffeaa7;
        }

        .formation-item:last-child {
            border-bottom: none;
        }

        .formation-name {
            font-weight: 600;
            color: #000;
            margin-bottom: 4px;
        }

        .formation-details {
            font-size: 0.9rem;
            color: #666;
        }

        .formation-places {
            display: inline-block;
            background-color: #ffcc00;
            color: #000;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: #000;
            color: #ffcc00;
        }

        .btn-primary:hover {
            background-color: #333;
            color: #fff;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .confirmation-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }

        .confirmation-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .steps {
            counter-reset: step-counter;
            margin: 20px 0;
        }

        .step {
            counter-increment: step-counter;
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .step::before {
            content: counter(step-counter);
            background-color: #ffcc00;
            color: #000;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .payment-methods {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Paiement de votre inscription
        </div>
        
        <div class="payment-card">
            <div class="payment-info">
                <div class="form-row" style="margin-bottom: 15px;">
                    <span class="info-label">🎓 Formations sélectionnées :</span>
                </div>
                
                @php
                    $formations = json_decode($registration->label_formation, true);
                    $totalPlaces = 0;
                    $isValidFormationArray = false;
                    
                    // Vérifier si c'est un tableau valide de formations
                    if (is_array($formations) && count($formations) > 0) {
                        // Vérifier si le premier élément a les clés attendues
                        if (isset($formations[0]) && is_array($formations[0]) && isset($formations[0]['nom'])) {
                            $isValidFormationArray = true;
                        }
                    }
                @endphp
                
                @if ($isValidFormationArray)
                    <div class="formation-list">
                        @foreach ($formations as $formation)
                            @php
                                $places = $formation['nombre_places'] ?? 1;
                                $totalPlaces += $places;
                            @endphp
                            <div class="formation-item">
                                <div class="formation-name">
                                    {{ $formation['nom'] ?? 'Formation' }}
                                </div>
                                <div class="formation-details">
                                    <span class="formation-places">{{ $places }} place{{ $places > 1 ? 's' : '' }}</span>
                                    @if (isset($formation['date']))
                                        <span style="margin-left: 10px;">📅 {{ $formation['date'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background-color: #fff3cd; border-left: 4px solid #ffcc00; padding: 15px; border-radius: 4px; margin: 15px 0; color: #666; font-style: italic;">
                        {{ $registration->label_formation }}
                    </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Nom complet :</span>
                    <span>{{ $registration->nom_complet }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nombre total de places :</span>
                    <span><strong>{{ $registration->nombre_tickets }} place{{ $registration->nombre_tickets > 1 ? 's' : '' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Montant total :</span>
                    <span><strong style="color: #000; font-size: 1.2rem;">{{ number_format($registration->montant, 0, ',', ' ') }} FCFA</strong></span>
                </div>
                <div class="info-row" style="border-top: 1px solid #ddd; padding-top: 10px; margin-top: 10px;">
                    <span class="info-label">Référence de transaction :</span>
                    <span><code style="background-color: #f0f0f0; padding: 4px 8px; border-radius: 4px;">{{ $registration->transaction_id }}</code></span>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Information importante :</strong> Choisissez votre mode de paiement ci-dessous. Après paiement, vous devrez confirmer la transaction pour recevoir votre ticket.
            </div>

            <h3>Choisissez votre mode de paiement :</h3>
            
            <div class="payment-methods">
                <div class="payment-option" onclick="selectPayment('mobile')">
                    <h3>💱 Mobile Money</h3>
                    <p>Orange Money, MTN Money, Moov Money</p>
                </div>
                
                <div class="payment-option" onclick="selectPayment('bank')">
                    <h3>🏦 Virement Bancaire</h3>
                    <p>GTBank CI via QR Code</p>
                </div>
            </div>

			<!-- Section QR Code Mobile Money -->
			<div id="mobile-qr" class="qr-section">
				<h3>🔄 Paiement Mobile Money</h3> 
				
				<h4>Effectuez votre paiement :</h4>
				<a href="https://ebank.gtbankci.com/MerchandQRCode/Home/PayMe?pay2=2cc60a88-68d4-48d7-abcc-f82ed5922083" target="_blank" class="btn btn-primary">
					Payez ici
				</a>
				<h4>------------- Ou -------------</h4>
				<div class="steps">
					<div class="step">Scannez le QR Code ci-dessous (NB : Utilisez votre caméra ou votre application dédiée à la numérisation de QR CODE.)</div>
					<div class="step">Validez le paiement de <strong>{{ number_format($registration->montant, 0, ',', ' ') }} FCFA</strong></div>
					<div class="step">Confirmez ci-dessous après paiement</div>
				</div>
				<div class="qr-code">
					<img src="{{ asset('img/QrCodeGTBank.jpeg') }}" alt="QR Code GTBank" style="max-width: 200px; max-height: 200px;" />
				</div>
			</div>

            <!-- Section QR Code Banque -->
            <div id="bank-qr" class="qr-section">
                <h3>🏦 Paiement GTBank CI</h3>
                <div class="steps">
                    <div class="step">Scannez le QR Code ci-dessous</div>
                    <div class="step">Validez le paiement de <strong>{{ number_format($registration->montant, 0, ',', ' ') }} FCFA</strong></div>
                    <div class="step">Notez la référence de transaction</div>
                </div>
                
                <div class="qr-code">
                    <img src="{{ asset('img/gtbank-qr.png') }}" alt="QR Code GTBank" style="max-width: 200px; max-height: 200px;" />
                </div>
                
                <div class="alert alert-warning">
                    <strong>Informations bancaires :</strong><br>
                    Bénéficiaire : DC-KNOWING CGA<br>
                    Référence : {{ $registration->transaction_id }}
                </div>
            </div>

            <!-- Formulaire de confirmation -->
            <div id="confirmation-form" class="confirmation-form">
                <h3>✅ Confirmer votre paiement</h3>
                <p>Une fois votre paiement effectué, veuillez remplir les informations ci-dessous :</p>
                
                <form action="{{ route('form.confirmpayment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="registration_id" value="{{ $registration->id }}">
                    
                    <div class="form-group">
                        <label for="payment_reference">Référence de la transaction *</label>
                        <input type="text" id="payment_reference" name="payment_reference" class="form-control" required 
                               placeholder="Ex: MP250814.1633.... ou numéro de confirmation">
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Mode de paiement utilisé *</label>
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="">Choisir...</option>
                            <option value="orange_money">Orange Money</option>
                            <option value="mtn_money">MTN Money</option>
                            <option value="moov_money">Moov Money</option>
                            <option value="wave_money">Wave</option>
                            <option value="gtbank">GTBank CI</option>
                            <option value="autre_banque">Espèce</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_amount">Montant payé (FCFA) *</label>
                        <input type="number" id="payment_amount" name="payment_amount" class="form-control" 
                               value="{{ $registration->montant }}" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_comment">Commentaire (optionnel)</label>
                        <textarea id="payment_comment" name="payment_comment" class="form-control" rows="3" 
                                  placeholder="Ajoutez des informations complémentaires si nécessaire"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 10px;">
                        ✅ Confirmer le paiement et générer mon ticket
                    </button>
                </form>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('form.index') }}" class="btn btn-primary">
                    ← Retour au formulaire
                </a>
            </div>
        </div>
    </div>

    <script>
        function selectPayment(type) {
            // Masquer toutes les sections QR
            document.querySelectorAll('.qr-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Réinitialiser les options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Activer l'option sélectionnée
            event.target.closest('.payment-option').classList.add('selected');
            
            // Afficher la section correspondante
            if (type === 'mobile') {
                document.getElementById('mobile-qr').classList.add('active');
            } else if (type === 'bank') {
                document.getElementById('bank-qr').classList.add('active');
            }
            
            // Afficher le formulaire de confirmation
            document.getElementById('confirmation-form').classList.add('active');
            
            // Scroll vers la section QR
            setTimeout(() => {
                document.querySelector('.qr-section.active').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }

        // Auto-remplir le mode de paiement selon la sélection
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentMethodSelect = document.getElementById('payment_method');
            
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    if (this.textContent.includes('Mobile Money')) {
                        paymentMethodSelect.value = 'orange_money'; // Par défaut
                    } else if (this.textContent.includes('Bancaire')) {
                        paymentMethodSelect.value = 'gtbank';
                    }
                });
            });
        });
    </script>
</body>
</html>