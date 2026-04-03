<div class="modal-header">
    <h5 class="modal-title" id="modelHeading">@lang('app.view') @lang('Détails de l\'entreprise')</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">×</span></button>
</div>
<div class="modal-body bg-additional-grey">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card border-0 b-shadow-4">
            <div class="card-horizontal align-items-center">
                <div class="card-body border-0 pr-0">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <x-cards.data-row :label="__('Dénomination Sociale')" :value="$entreprise->denomination_sociale ?? '--'" />
                            <x-cards.data-row :label="__('Forme Juridique')" :value="$entreprise->forme_juridique ?? '--'" />
                            <x-cards.data-row :label="__('Capital Social')" :value="$entreprise->capital_social ? number_format($entreprise->capital_social, 2) . ' ' . company()->currency->currency_code : '--'" />
                            <x-cards.data-row :label="__('Objet Social')" :value="$entreprise->objet_social ?? '--'" />
                            <x-cards.data-row :label="__('Siège Social')" :value="$entreprise->siege_social ?? '--'" />
                            <x-cards.data-row :label="__('Ville')" :value="$entreprise->ville ?? '--'" />
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <x-cards.data-row :label="__('app.client')" :value="$entreprise->user->name ?? '--'" />
                            <x-cards.data-row :label="__('app.telephone')" :value="$entreprise->telephone ?? '--'" />
                            <x-cards.data-row :label="__('app.email')" :value="$entreprise->email ?? '--'" />
                            <x-cards.data-row :label="__('app.status')" :value="($entreprise->statut) ? __('app.' . $entreprise->statut) : '--'" />
                            <x-cards.data-row :label="__('Date Demande')" :value="$entreprise->date_demande ? \Carbon\Carbon::parse($entreprise->date_demande)->translatedFormat(company()->date_format) : '--'" />
                            <x-cards.data-row :label="__('app.createdAt')" :value="$entreprise->created_at->translatedFormat(company()->date_format)" />
                        </div>
                    </div>

                    @if($entreprise->piece_identite || $entreprise->justificatif_domicile || $entreprise->autres_documents)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="f-16 f-w-500 text-dark-grey mb-3">@lang('Documents')</h4>
                            </div>
                            @if($entreprise->piece_identite)
                                <div class="col-md-4 mb-3">
                                    <x-cards.data-row :label="__('Pièce d\'identité')" :value="'<a href=' . asset_url('entreprise-docs/' . $entreprise->piece_identite) . ' class=\'text-primary\' target=\'_blank\'><i class=\'fa fa-download mr-1\'></i>' . __('app.download') . '</a>'" :html="true" />
                                </div>
                            @endif
                            @if($entreprise->justificatif_domicile)
                                <div class="col-md-4 mb-3">
                                    <x-cards.data-row :label="__('Justificatif domicile')" :value="'<a href=' . asset_url('entreprise-docs/' . $entreprise->justificatif_domicile) . ' class=\'text-primary\' target=\'_blank\'><i class=\'fa fa-download mr-1\'></i>' . __('app.download') . '</a>'" :html="true" />
                                </div>
                            @endif
                            @if($entreprise->autres_documents)
                                <div class="col-md-4 mb-3">
                                    <x-cards.data-row :label="__('Autres documents')" :value="'<a href=' . asset_url('entreprise-docs/' . $entreprise->autres_documents) . ' class=\'text-primary\' target=\'_blank\'><i class=\'fa fa-download mr-1\'></i>' . __('app.download') . '</a>'" :html="true" />
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <x-forms.button-cancel data-dismiss="modal" class="border-0">@lang('app.close')</x-forms.button-cancel>
</div>
