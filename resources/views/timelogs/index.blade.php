
@php
    use App\Models\Registration;
    use App\Models\Formulaire;

    $pendingPayments = Registration::with('tickets')
        ->where('montant', '>=', 10000)
        ->orderBy('created_at', 'desc')
        ->get();
                                 
    $pageTitle = ' Administration - Validation des paiements';
@endphp

@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@php
    $addTimelogPermission = user()->permission('add_timelogs');
@endphp


@section('content')
    
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background-color: #ffcc00;
            color: #000;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .payments-table {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .payment-details {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 0.875rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
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
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filters {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            table {
                font-size: 0.875rem;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
    <div class="mb-4">
            
    </div>
    <div class="content-wrapper">

        <div class="header">
            🎫 Administration - Validation des paiements
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $pendingPayments->where('statut', 'pending')->count() }}</div>
                <div class="stat-label">Paiements en attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $pendingPayments->where('statut', 'payé')->count() }}</div>
                <div class="stat-label">Paiements validés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($pendingPayments->where('statut', 'payé')->sum('montant'), 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Montant total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $pendingPayments->sum('nombre_tickets') }}</div>
                <div class="stat-label">Total tickets</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <h3>🔍 Filtres</h3>
            <div class="filters-grid">
                <div class="form-group">
                    <label>Statut</label>
                    <select class="form-control" id="filter-status">
                        <option value="">Tous</option>
                        <option value="pending">En attente validation</option>
                        <option value="payé">Validé</option>
                        <option value="paiement_rejete">Rejeté</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Méthode de paiement</label>
                    <select class="form-control" id="filter-method">
                        <option value="">Toutes</option>
                        <option value="orange_money">Orange Money</option>
                        <option value="mtn_money">MTN Money</option>
                        <option value="moov_money">Moov Money</option>
                        <option value="gtbank">GTBank CI</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" id="filter-date">
                </div>
                <div class="form-group">
                    <label>Recherche</label>
                    <input type="text" class="form-control" id="filter-search" placeholder="Nom, email, référence...">
                </div>
            </div>
            <button onclick="exportTableToExcel('payments-table', 'export_excel')" class="btn btn-success">
                Exporter en Excel
            </button>
            <!--<button onclick="exportTableToPDF('payments-table')" class="btn btn-danger">
                Exporter en PDF
            </button>-->
        </div>

        <!-- Liste des paiements -->
        <div class="payments-table">
            <table id="payments-table" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Participant</th>
                        <th>Contact</th>
                        <th>Tickets</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Référence</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayments as $payment)
                    <tr data-status="{{ $payment->statut }}" data-method="{{ $payment->methode_paiement ?? '' }}">
                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $payment->nom_complet }}</strong>
                            <div style="font-size: 0.8em; color: #666;">
                                 <strong>
                                    @foreach(json_decode($payment->nom_diplome, true) as $id => $diplome)
                                       🎫 {{ $diplome }}<br>
                                    @endforeach
                                </strong>
                            </div>
                            <div style="font-size: 0.8em; color: #666;">
                                ID: {{ $payment->transaction_id }}
                            </div>
                        </td>
                        <td>
                            <div>📧 {{ $payment->email }}</div>
                            <div>📱 {{ $payment->telephone }}</div>
                        </td>
                        <td>{{ $payment->nombre_tickets }}</td>
                        <td><strong>{{ number_format($payment->montant, 0, ',', ' ') }} FCFA</strong></td>
                        <td>
                            @if($payment->methode_paiement)
                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $payment->methode_paiement)) }}</span>
                            @else
                                <span class="badge badge-warning">Non spécifié</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->reference_paiement)
                                <code>{{ $payment->reference_paiement }}</code>
                            @else
                                <em>-</em>
                            @endif
                        </td>
                        <td>
                            @if($payment->statut === 'pending')
                                <span class="badge badge-warning">⏳ En attente</span>
                            @elseif($payment->statut === 'payé')
                                <span class="badge badge-success">✅ Validé</span>
                            @elseif($payment->statut === 'paiement_rejete')
                                <span class="badge badge-danger">❌ Rejeté</span>
                            @else
                                <span class="badge badge-warning">{{ $payment->statut }}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->statut === 'pending')
                                <button class="btn btn-success" onclick="validatePayment({{ $payment->id }})">
                                    ✅
                                </button>
                                <button class="btn btn-danger" onclick="rejectPayment({{ $payment->id }})">
                                    ❌
                                </button>
                            @endif
                            <button class="btn btn-info" onclick="showPaymentDetails({{ $payment->id }})">
                               👁️
                            </button>
                            @if($payment->ticket_number)
                                <a href="{{ route('form.download', $payment->ticket_number) }}" class="btn btn-info">
                                    📥 Ticket
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px;">
                            <div style="color: #666;">
                                📭 Aucun paiement en attente
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de rejet -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRejectModal()">&times;</span>
            <h3>❌ Rejeter le paiement</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label for="reject-reason">Raison du rejet *</label>
                    <textarea id="reject-reason" name="commentaire" class="form-control" rows="4" required 
                              placeholder="Expliquez pourquoi ce paiement est rejeté..."></textarea>
                </div>
                <div style="text-align: right; margin-top: 20px;">
                    <button type="button" class="btn btn-info" onclick="closeRejectModal()">Annuler</button>
                    <button type="submit" class="btn btn-danger">❌ Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal détails -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailsModal()">&times;</span>
            <h3>📋 Détails du paiement</h3>
            <div id="payment-details-content">
                <!-- Contenu dynamique -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS pour Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <!-- JS pour PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        // === EXPORT EXCEL ===
        function exportTableToExcel(tableID, filename = ''){
            let table = document.getElementById(tableID);
            let wb = XLSX.utils.table_to_book(table, {sheet: "Feuille1"});
            return XLSX.writeFile(wb, filename ? filename + ".xlsx" : "export.xlsx");
        }
        
        // === EXPORT PDF ===
        function exportTableToPDF(tableID){
            const { jsPDF } = window.jspdf;
            var doc = new jsPDF('p','pt','a4');
            doc.text("Exportation du tableau", 40, 40);
            doc.autoTable({ html: '#'+tableID, startY: 60 });
            doc.save("export.pdf");
        }
        // Données des paiements pour JavaScript
        const paymentsData = @json($pendingPayments);

        // Fonctions de validation
		function validatePayment(paymentId) {
			if (confirm('Êtes-vous sûr de vouloir valider ce paiement ? Un ticket sera automatiquement généré.')) {
				const url = "{{ route('timelogs.validatePayment', ':id') }}".replace(':id', paymentId);

				$.ajax({
					url: url,
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
					},
					success: function(data) {
						if (data.success) {
							alert('✅ Paiement validé avec succès !');
							location.reload();
						} else {
							alert('❌ Erreur : ' + data.message);
						}
					},
					error: function(xhr) {
						alert('❌ Erreur serveur : ' + xhr.responseText);
						console.error(xhr);
					}
				});
			}
		}


        // Modal de rejet
        function showRejectModal(paymentId) {
            document.getElementById('rejectForm').action = `/timelogs/payments/${paymentId}/reject`;
            document.getElementById('rejectModal').style.display = 'block';
        }
		
		function rejectPayment(paymentId) {
			const url = "{{ route('timelogs.rejectPayment', ':id') }}".replace(':id', paymentId);

			$.ajax({
				url: url,
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
				},
				data: {
					commentaire: document.getElementById('commentaire').value
				},
				success: function(data) {
					if (data.success) {
						alert('🚫 Paiement rejeté avec succès !');
						location.reload();
					} else {
						alert('❌ Erreur : ' + data.message);
					}
				},
				error: function(xhr) {
					alert('❌ Erreur serveur : ' + xhr.responseText);
				}
			});
		}


        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Modal détails
        function showPaymentDetails(paymentId) {
            const payment = paymentsData.find(p => p.id === paymentId);
            if (!payment) return;

            const content = `
                <div class="payment-details">
                    <h4>🎫 Informations générales</h4>
                    <p><strong>Nom complet :</strong> ${payment.nom_complet}</p>
                    <p><strong>Email :</strong> ${payment.email || 'Non fourni'}</p>
                    <p><strong>Téléphone :</strong> ${payment.telephone || 'Non fourni'}</p>
                    <p><strong>Nombre de tickets :</strong> ${payment.nombre_tickets}</p>
                    <p><strong>Montant :</strong> ${parseInt(payment.montant).toLocaleString()} FCFA</p>
                </div>

                <div class="payment-details">
                     <h4>💳 Informations de paiement</h4>
                    <p><strong>Transaction ID :</strong> ${payment.transaction_id}</p>
                    <p><strong>Référence paiement :</strong> ${payment.reference_paiement || 'Non fournie'}</p>
                    <p><strong>Méthode :</strong> ${payment.methode_paiement ? payment.methode_paiement.replace('_', ' ') : 'Non spécifiée'}</p>
                    <p><strong>Date inscription :</strong> ${new Date(payment.created_at).toLocaleString('fr-FR')}</p>
                    <p><strong>Date paiement :</strong> ${payment.date_paiement ? new Date(payment.date_paiement).toLocaleString('fr-FR') : 'En attente'}</p>
                </div>

                <div class="payment-details">
                    <h4>📋 Statut et actions</h4>
                    <p><strong>Statut actuel :</strong> 
                        <span class="badge badge-${payment.statut === 'paiement_confirme' ? 'warning' : payment.statut === 'payé' ? 'success' : 'danger'}">
                            ${payment.statut}
                        </span>
                    </p>
                    ${payment.ticket_number ? `<p><strong>N° Ticket :</strong> ${payment.ticket_number}</p>` : ''}
                    ${payment.commentaire_admin ? `<p><strong>Commentaire admin :</strong> ${payment.commentaire_admin}</p>` : ''}
                </div>
            `;

            document.getElementById('payment-details-content').innerHTML = content;
            document.getElementById('detailsModal').style.display = 'block';
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Filtrage
        function filterTable() {
            const statusFilter = document.getElementById('filter-status').value;
            const methodFilter = document.getElementById('filter-method').value;
            const dateFilter = document.getElementById('filter-date').value;
            const searchFilter = document.getElementById('filter-search').value.toLowerCase();

            const rows = document.querySelectorAll('#payments-table tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // Ligne "aucun résultat"
                
                const status = row.dataset.status;
                const method = row.dataset.method;
                const rowDate = row.cells[0].textContent;
                const searchText = row.textContent.toLowerCase();

                let show = true;

                if (statusFilter && status !== statusFilter) show = false;
                if (methodFilter && method !== methodFilter) show = false;
                if (searchFilter && !searchText.includes(searchFilter)) show = false;
                if (dateFilter) {
                    const filterDate = new Date(dateFilter).toLocaleDateString('fr-FR');
                    if (!rowDate.includes(filterDate.split(' ')[0])) show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        // Event listeners pour les filtres
        document.getElementById('filter-status').addEventListener('change', filterTable);
        document.getElementById('filter-method').addEventListener('change', filterTable);
        document.getElementById('filter-date').addEventListener('change', filterTable);
        document.getElementById('filter-search').addEventListener('input', filterTable);

        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const detailsModal = document.getElementById('detailsModal');
            
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
            if (event.target === detailsModal) {
                detailsModal.style.display = 'none';
            }
        }

        // Auto-refresh toutes les 30 secondes
        setInterval(() => {
            const currentSearch = document.getElementById('filter-search').value;
            if (!currentSearch) {
                location.reload();
            }
        }, 30000);
    </script>
@endpush
