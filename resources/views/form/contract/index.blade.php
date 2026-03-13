<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Formulaire d'adhésion au CGA</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />


    <!-- Template CSS -->
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <!-- Simple Line Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/simple-line-icons.css') }}" defer="defer">

    <!-- Datepicker -->
    <link rel="stylesheet" href="{{ asset('vendor/css/datepicker.min.css') }}" defer="defer">

    <!-- TimePicker -->
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-timepicker.min.css') }}" defer="defer">

    <!-- Select Plugin -->   
    <link rel="stylesheet" href="{{ asset('vendor/css/select2.min.css') }}" defer="defer">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-icons.css') }}" defer="defer">
    <link rel='stylesheet' href="{{ asset('vendor/css/dragula.css') }}" type='text/css' />
    <link rel='stylesheet' href="{{ asset('vendor/css/drag.css') }}" type='text/css' />

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ isset($company)?$company->favicon_url:global_setting()->favicon_url }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="16x16"
             href="{{ isset($company)?$company->favicon_url:global_setting()->favicon_url }}">

    @include('sections.theme_css')

    @isset($activeSettingMenu)
        <style>
            .preloader-container {
                margin-left: 510px;
                width: calc(100% - 510px)
            }

        </style>
    @endisset

    @stack('styles')

    <style>
        :root {
            --fc-border-color: #E8EEF3;
            --fc-button-text-color: #99A5B5;
            --fc-button-border-color: #99A5B5;
            --fc-button-bg-color: #ffffff;
            --fc-button-active-bg-color: #171f29;
            --fc-today-bg-color: #f2f4f7;
        }

        .preloader-container {
            height: 100vh;
            width: 100%;
            margin-left: 0;
            margin-top: 0;
        }

        .rtl .preloader-container {
            margin-right: 0;
        }

        .fc a[data-navlink] {
            color: #99a5b5;
        }

        .b-p-tasks {
            min-height: 90%;
        }

    </style>
    <style>
        #logo {
            height: 50px;
        }

    </style>


    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/modernizr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="path/to/easyAjax.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var checkMiniSidebar = localStorage.getItem("mini-sidebar");
    </script>
</head>
<body>
    @php
        $lastGeneratedNumber = DB::table('client_details')
            ->select('id')
            ->orderBy('id', 'DESC')
            ->first();

        $newNumber = $lastGeneratedNumber->id + 1;
        $formattedNumber = sprintf('DCK-%03d', $newNumber);

        // Récupérer le dernier numéro généré
        $lastGeneratedNumber1 = DB::table('client_details')
            ->select('numadh')
            ->orderBy('numadh', 'DESC')
            ->first();

        // Initialiser le nouveau numéro
        $newNumber1 = 1; // Commencer à 001 par défaut
        $currentYear = date('Y');

        if ($lastGeneratedNumber1) {
            // Extraire l'année du dernier numéro
            $lastYear = substr($lastGeneratedNumber1->numadh, 8, 4); // Supposons que l'année commence à la 8ème position

            // Vérifier si l'année du dernier numéro est la même que l'année actuelle
            if ($lastYear == $currentYear) {
                $derniersNumeros = intval(substr($lastGeneratedNumber1->numadh, -3));
                $newNumber1 = $derniersNumeros + 1;
            }
        }

        // Formater le nouveau numéro
        $formattedNumber1 = sprintf('DCK-CGA%s-A%03d', $currentYear, $newNumber1);
    @endphp

    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="banniere">
                        <img src="{{ asset('img/img.jpg') }}" alt="Banniére de Suivi"><!-- Afficher l'image -->
                    </div><p></p>
                    <h4 align="center" class="mb-3" style="background-color:yellow;">Formulaire d'adhésion au CGA DC-KNOWING</h4>
                    @if (session('success'))
                        <div class="col-md-12">
                            <div class="form-group my-3">
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                    @endif              
                    <form action="{{ route('form.contract.store') }}" method="POST" id="save-contract-data-form" class="ajax-form">
                        <div class="add-client bg-white rounded">
                            {{--auth()->id()--}} 
                            <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                                @lang('Détails Client')
                            </h4> 
                            <div class="row p-20">
                                <div class="col-md-3">
                                    <div class="form-group text-left">
                                        <label for="name">@lang('app.name') du dirigeant <sup class="f-14 mr-1">*</sup></label>
                                        <input type="text" tabindex="1" name="name"
                                            class="form-control height-40 f-13 light_text"
                                            placeholder="@lang('placeholders.name')" id="name" autofocus>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group text-left">
                                        <label for="email">@lang('auth.email') <sup class="f-14 mr-1">*</sup></label>
                                        <input tabindex="2" type="email" name="email"
                                            class="form-control height-40 f-13 light_text"
                                            placeholder="@lang('placeholders.email')" id="email">
                                        <input type="hidden" id="g_recaptcha" name="g_recaptcha">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group text-left">
                                        <label for="password">@lang('app.password') <sup class="f-14 mr-1">*</sup></label>
                                        <x-forms.input-group>
                                            <input type="password" name="password" id="password"
                                                placeholder="@lang('placeholders.password')" tabindex="3"
                                                class="form-control height-40 f-13 light_text">
                                            <x-slot name="append">
                                                <button type="button" tabindex="4" data-toggle="tooltip"
                                                        data-original-title="@lang('app.viewPassword')"
                                                        class="btn btn-outline-secondary border-grey height-40 toggle-password">
                                                    <i
                                                        class="fa fa-eye"></i></button>
                                            </x-slot>
                                        </x-forms.input-group>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group text-left">
                                        <label for="company_name">@lang('modules.client.companyName') <sup class="f-14 mr-1">*</sup></label>
                                        <input type="text" tabindex="5" name="company_name"
                                            class="form-control height-40 f-13 light_text"
                                            placeholder="@lang('placeholders.company')" id="company_name">
                                    </div> 
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group text-left">
                                        <label for="numadh">@lang('Numéro d\'adhésion CG') <sup class="f-14 mr-1">*</sup></label>
                                        <input type="text" tabindex="5" name="numadh" class="form-control height-40 f-13 light_text"
                                            id="numadh" Value="{{$formattedNumber1}}" readonly>
                                    </div> 
                                </div>

                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="website"
                                        :fieldLabel="__('modules.client.website')" fieldName="website"
                                        :fieldPlaceholder="__('placeholders.website')" :fieldValue="$lead->website ?? ''">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="tax_name"
                                        :fieldLabel="__('Numéro RCCM')" fieldName="tax_name"
                                        :fieldPlaceholder="__('Numéro RCCM')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="gst_number"
                                        :fieldLabel="__('Numéro Compte Contribuable')" fieldName="gst_number"
                                        :fieldPlaceholder="__('Numéro Compte Contribuable')">
                                    </x-forms.text>
                                </div>

                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numrccm"
                                        :fieldLabel="__('Numéro IDU')" fieldName="numrccm"
                                        :fieldPlaceholder="__('Numéro IDU')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">                        
                                    <x-forms.label class="mb-3 mt-2 mt-lg-0 mt-md-0" fieldId="formjurid"
                                        :fieldLabel="__('Forme juridique')">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select id="formjurid" name="formjurid" class="dropdown form-control  height-35" data-live-search="true">
                                            <option value="">--</option>
                                            <option value="ENTREPRISE INDIVIDUELLE">ENTREPRISE INDIVIDUELLE</option>
                                            <option value="SARL">SARL</option>
                                            <option value="SAS">SAS</option>  
                                            <option value="SA">SA</option>
                                            <option value="SNC">SNC</option>
                                            <option value="NCS">NCS</option>
                                            <option value="ONG">ONG</option>
                                            <option value="AUTRE">AUTRE</option>
                                        </select>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-3">
                                    <x-forms.label class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="regime"
                                        :fieldLabel="__('Régime fiscal')">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select id="regime" name="regime" class="dropdown form-control  height-35" data-live-search="true">
                                            <option value="">--</option>
                                            <option value="TEE">TEE</option>
                                            <option value="RME">RME</option>
                                            <option value="RSI">RSI</option>
                                            <option value="RNI">RNI</option>
                                        </select>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-3">
                                    <x-forms.label class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="imp_centre"
                                        :fieldLabel="__('Centre des impôts')">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select id="imp_centre" name="imp_centre" class="dropdown form-control  height-35" data-live-search="true">
                                            <option value="">--</option>
                                            <option value="II Plateaux III">II Plateaux III</option>
                                            <option value="II Plateaux Djibi">II Plateaux Djibi</option>
                                            <option value="II Pateaux I">II Plateaux I</option>
                                            <option value="II Pateaux II">II Plateaux II</option>
                                            <option value="Anyama">Anyama</option>
                                            <option value="Alepé">Alepé</option>
                                            <option value="Abobo II">Abobo II</option>
                                            <option value="Abobo III">Abobo III</option>
                                            <option value="Adjamé I">Adjamé I</option>
                                            <option value="Adjamé II">Adjamé II</option>
                                            <option value="Attecoubé">Attecoubé</option>
                                            <option value="Adjamé III">Adjamé III</option>
                                            <option value="Cocody">Cocody</option>
                                            <option value="Williamsvile">Williamsvile</option>
                                            <option value="Plateau I">Plateau I</option>
                                            <option value="Plateau II">Plateau II</option>
                                            <option value="Yopougon I">Yopougon I</option>
                                            <option value="Yopougon III">Yopougon III</option>
                                            <option value="Yopougon III">Yopougon III</option>
                                            <option value="Yopougon V">Yopougon V</option>
                                            <option value="Yopougon IV">Yopougon IV</option>
                                            <option value="Bingerville">Bingerville</option>
                                            <option value="Riviera I">Riviera I</option>
                                            <option value="Riviera II">Riviera II</option>
                                            <option value="Port-Bouet">Port-Bouet</option>
                                            <option value="Treichville I">Treichville I</option>
                                            <option value="Treichville II">Treichville II</option>
                                            <option value="Bietry">Bietry</option>
                                            <option value="Koumassi I">Koumassi I</option>
                                            <option value="Koumassi II">Koumassi II</option>
                                            <option value="Marcory I">Marcory I</option>
                                            <option value="Marcory II">Marcory II</option>
                                            <option value="Zone IV">Zone IV</option>
                                            <option value="Abengourou">Abengourou</option>
                                            <option value="Agnibilekro">Agnibilekro</option>
                                            <option value="Betié">Betié</option>
                                            <option value="Niablé">Niablé</option>
                                            <option value="Aboisso">Aboisso</option>
                                            <option value="Adiaké">Adiaké</option>
                                            <option value="Bonoua">Bonoua</option>
                                            <option value="Grand Bassam">Grand Bassam</option>
                                            <option value="Tiapoum">Tiapoum</option>
                                            <option value="Adzopé">Adzopé</option>
                                            <option value="Agboville">Agboville</option>
                                            <option value="Akoupé">Akoupé</option>
                                            <option value="Taabo">Taabo</option>
                                            <option value="Tiassalé">Tiassalé</option>
                                            <option value="Yakassé">Yakassé</option>
                                            <option value="Bondoukou">Bondoukou</option>
                                            <option value="Doropo">Doropo</option>
                                            <option value="Koun Fao">Koun Fao</option>
                                            <option value="Kouassi-Datékro">Kouassi-Datékro</option>
                                            <option value="Nassian">Nassian</option>
                                            <option value="Tanda">Tanda</option>
                                            <option value="Bouaké I">Bouaké I</option>
                                            <option value="Dabakala">Dabakala</option>
                                            <option value="katiola">katiola</option>
                                            <option value="M’Bahiakro">M’Bahiakro</option>
                                            <option value="Niakara">Niakara</option>
                                            <option value="Bouaké I">Bouaké I</option>
                                            <option value="Bouaké II">Bouaké II</option>
                                            <option value="béoumi">béoumi</option>
                                            <option value="Sakassou">Sakassou</option>
                                            <option value="Dabou">Dabou</option>
                                            <option value="Grand Lahou">Grand Lahou</option>
                                            <option value="Jacqueville">Jacqueville</option>
                                            <option value="Sikensi">Sikensi</option>
                                            <option value="Songon">Songon</option>
                                            <option value="Daloa I">Daloa I</option>
                                            <option value="Daloa II">Daloa II</option>
                                            <option value="Issia">Issia</option>
                                            <option value="Mankono">Mankono</option>
                                            <option value="Seguela">Seguela</option>
                                            <option value="Vavoua">Vavoua</option>
                                            <option value="Arrah">Arrah</option>
                                            <option value="Bocanda">Bocanda</option>
                                            <option value="Bongouanou">Bongouanou</option>
                                            <option value="Dimbokro">Dimbokro</option>
                                            <option value="Daoukro">Daoukro</option>
                                            <option value="M’Batto">M’Batto</option>
                                            <option value="Divo">Divo</option>
                                            <option value="Gagnoa">Gagnoa</option>
                                            <option value="Oumé">Oumé</option>
                                            <option value="Guiglo">Guiglo</option>
                                            <option value="Boundiali">Boundiali</option>
                                            <option value="Dikodougou">Dikodougou</option>
                                            <option value="Ferkessedougou">Ferkessedougou</option>
                                            <option value="kong">kong</option>
                                            <option value="Korhogo">Korhogo</option>
                                            <option value="M’Bengue">M’Bengue</option>
                                            <option value="Ouangolodougou">Ouangolodougou</option>
                                            <option value="Tengrela">Tengrela</option>
                                            <option value="Odienne">Odienne</option>
                                            <option value="Touba">Touba</option>
                                            <option value="Man">Man</option>
                                            <option value="Danane">Danane</option>
                                            <option value="Bangolo">Bangolo</option>
                                            <option value="Fresco">Fresco</option>
                                            <option value="San pedro I">San pedro I</option>
                                            <option value="San pedro II">San pedro II</option>
                                            <option value="Tabou">Tabou</option>
                                            <option value="Soubre">Soubre</option>
                                            <option value="Bouafle">Bouafle</option>
                                            <option value="Tiebissou">Tiebissou</option>
                                            <option value="Toumodi">Toumodi</option>
                                            <option value="Youmoussoukro">Youmoussoukro</option>
                                            <option value="Zeunoula">Zeunoula</option>
                                        </select>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-3">
                                    <x-forms.text class="mb-4 mt-3 mt-lg-0 mt-md-0" fieldId="acti_prin"
                                        :fieldLabel="__('Activités principales')" fieldName="acti_prin"
                                        :fieldPlaceholder="__('Activités principales')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="section"
                                        :fieldLabel="__('Section')" fieldName="section"
                                        :fieldPlaceholder="__('Section')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="parcelle"
                                        :fieldLabel="__('Parcelle')" fieldName="parcelle"
                                        :fieldPlaceholder="__('Parcelle')">
                                    </x-forms.text>
                                </div>
                                
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="codeacti"
                                        :fieldLabel="__('Code activité')" fieldName="codeacti"
                                        :fieldPlaceholder="__('Code activité')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="montcapit"
                                        :fieldLabel="__('Montant du capital')" fieldName="montcapit"
                                        :fieldPlaceholder="__('Montant du capital')">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="office" :fieldLabel="__('Numéro WhatsApp')"
                                        fieldName="office" :fieldPlaceholder="__('placeholders.mobileWithPlus')" :fieldValue="$lead->office ?? ''">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="city" :fieldLabel="__('modules.stripeCustomerAddress.city')"
                                        fieldName="city" :fieldPlaceholder="__('placeholders.city')" :fieldValue="$lead->city ?? ''">
                                    </x-forms.text>
                                </div>
                                <div class="col-md-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="state" :fieldLabel="__('Sigle')"
                                        fieldName="state" :fieldPlaceholder="__('Sigle')" :fieldValue="$lead->state ?? ''">
                                    </x-forms.text>
                                </div>

                                <div class="col-lg-6 col-md-6" style="display:none;">
                                    <x-forms.select fieldId="added_by" :fieldLabel="__('app.added').' '.__('app.by')"
                                        fieldName="added_by">
                                        <option value="">--</option>
                                    </x-forms.select>
                                </div>

                                <div class="col-md-12">
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group my-3">
                                        <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                            :fieldLabel="__('modules.accountSettings.companyAddress')" fieldName="address"
                                            fieldId="address" :fieldPlaceholder="__('placeholders.address')"
                                            :fieldValue="$lead->address ?? ''">
                                        </x-forms.textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                                        :fieldLabel="__('Ajouter les fichiers suivants : DFE, RCCM, STATUT, CONTRAT DE BAIL')" fieldName="file"
                                                        fieldId="file-upload-dropzone"/>
                                    <input type="hidden" name="projectID" id="projectID">  
                                </div>
                            </div>

                            <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                                @lang('app.contractDetails')
                            </h4>

                            <div class="row p-20">
                                <!-- CONTRACT NUMBER START -->
                                <div class="col-md-3 col-lg-3  mb-4">
                                    <div class="form-group mb-lg-0 mb-md-0 mb-4">
                                        <x-forms.label class="mb-12" fieldId="contract_number"
                                            :fieldLabel="__('modules.contracts.contractNumber')" fieldRequired="true">
                                        </x-forms.label>
                                        <x-forms.input-group>
                                            <x-slot name="prepend">
                                                <span
                                                    class="input-group-text">{{ invoice_setting()->contract_prefix }}{{ invoice_setting()->contract_number_separator }}{{ $zero }}</span>
                                            </x-slot>
                                            <input type="number" name="contract_number" id="contract_number" class="form-control height-35 f-15"
                                                value="{{ is_null($lastContract) ? 1 : $lastContract }}">
                                        </x-forms.input-group>
                                    </div>
                                </div>
                                
                                <!--contrat client input-->

                                <div class="col-md-5 col-lg-5">
                                    <x-forms.label class="mb-12" fieldId="contractType"
                                                :fieldLabel="__('modules.contracts.contractType')"
                                                fieldRequired="true"></x-forms.label>
                                    <x-forms.input-group>
                                        <input type="text" id="contractType" name="contract_type" value="Nouvelle adhésion" readonly class="form-control height-35 f-14"/>
                                    </x-forms.input-group>
                                </div>

                                <!-- CONTRACT NUMBER END -->
                                <div class="col-md-6 col-lg-6" style="margin-top: -16px; display: none;">   
                                        <x-forms.text fieldId="subject" :fieldLabel="__('app.subject')" fieldName="subject"
                                        fieldRequired="true"></x-forms.text>
                                </div>  
                                <div class="col-md-6 col-lg-4">
                                    <x-forms.label class="mb-12" fieldId="amountctr"
                                                :fieldLabel="__('modules.contracts.contractValue')"
                                                :popover="__('modules.contracts.setZero')" fieldRequired="true"></x-forms.label>
                                    <x-forms.input-group>
                                        <input type="number" id="amountct" min="0" name="amount" value="25000" readonly class="form-control height-35 f-14"/>
                                    </x-forms.input-group>
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group mb-lg-0 mb-md-0 mb-4">
                                        <label for="start_date" class="mb-12">
                                            {{ __('modules.projects.startDate') }} <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="start_date" 
                                            name="start_date" 
                                            class="form-control height-35 f-14" 
                                            required 
                                            value="{{ ($contract && $contract->start_date ? $contract->start_date->format('Y-m-d') : '') }}" 
                                            placeholder="{{ __('placeholders.date') }}"
                                        />
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group mb-lg-0 mb-md-0 mb-4">
                                        <label for="end_date" class="mb-12">
                                            {{ __('modules.timeLogs.endDate') }} <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="date" 
                                            id="end_date" 
                                            name="end_date" 
                                            class="form-control height-35 f-14" 
                                            required 
                                            value="{{ ($contract && $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}" 
                                            placeholder="{{ __('placeholders.date') }}"
                                        />
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row p-20" style="justify-content: flex-end;">
                                <button type="submit" id="save-clientcontrat-form" class="btn btn-primary mr-3" icon="check">
                                    <i class="fa fa-check"></i> @lang('app.save')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        
        /*$(document).ready(function () {
            var contractTypeSelect = document.getElementById('contractType');
            const subjectInput = document.querySelector('[name="subject"]');
            const amountInput = document.querySelector('[name="amount"]');

            contractTypeSelect.addEventListener('change', function() {
                const selectedOption = contractTypeSelect.options[contractTypeSelect.selectedIndex];
                
                // Met à jour le champ sujet avec le nom de l'option sélectionnée
                subjectInput.value = selectedOption.text;
                amountInput.value = 25000;
                // Logique pour mettre à jour le champ montant selon l'option sélectionnée
                if (selectedOption.value == 3) { // Assurez-vous que vous comparez avec la valeur
                    amountInput.value = 25000; // Met à jour le montant
                } else {
                    amountInput.value = 0; // Réinitialise le montant
                }
            });
        });*/
        
        $(document).ready(function () {
            const dp1 = datepicker('#start_date', {
                position: 'bl',
                onSelect: (instance, date) => {
                    if (typeof dp2.dateSelected !== 'undefined' && dp2.dateSelected.getTime() < date
                        .getTime()) {
                        dp2.setDate(date, true)
                    }
                    if (typeof dp2.dateSelected === 'undefined') {
                        dp2.setDate(date, true)
                    }
                    dp2.setMin(date);
                },
                ...datepickerConfig
            });

            const dp2 = datepicker('#end_date', {
                position: 'bl',
                onSelect: (instance, date) => {
                    dp1.setMax(date);
                },
                ...datepickerConfig
            });

            $('.custom-date-picker').each(function(ind, el) {
                datepicker(el, {
                    position: 'bl',
                    ...datepickerConfig
                });
            });
        });
    </script>
</body>
</html>