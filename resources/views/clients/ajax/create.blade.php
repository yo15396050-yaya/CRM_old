@php
    $addClientCategoryPermission = user()->permission('manage_client_category');
    $addClientSubCategoryPermission = user()->permission('manage_client_subcategory');
    $addClientNotePermission = user()->permission('add_client_note');
    $addPermission = user()->permission('add_clients');
    use Illuminate\Support\Facades\DB;

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
        $lastYear = substr($lastGeneratedNumber1->numadh, 7, 4); // Année à la 8ème position (4 caractères)
        //echo $lastYear;
        // Vérifier si l'année du dernier numéro est la même que l'année actuelle
        if ($lastYear == $currentYear) {
            // Extraire le numéro à la fin du dernier numéro
            $derniersNumeros = intval(substr($lastGeneratedNumber1->numadh, -3)); // Numéro à la fin
            $newNumber1 = $derniersNumeros + 1; // Incrémenter le numéro
        }
    }
    // Formater le nouveau numéro
    $formattedNumber1 = sprintf('DCK-CGA%s-A%03d', $currentYear, $newNumber1);
    //echo "Nouveau numéro ADH : " . $formattedNumber;
@endphp

<link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-client-data-form">

            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('modules.employees.accountDetails')</h4>

                @if (isset($lead->id)) <input type="hidden" name="lead"
                        value="{{ $lead->id }}"> @endif

                <div class="row p-20">
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-4">                        
                                <x-forms.label class="mt-3" fieldId="formjurid"
                                    :fieldLabel="__('Forme juridique')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select id="formjurid" name="formjurid" class="form-control select-picker" data-live-search="true">
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
                            @if(in_array('apporteurs', user_roles()))
                                <div class="col-md-4">
                                    <x-forms.label class="mt-3" fieldId="category"
                                        :fieldLabel="__('modules.client.clientCategory')">
                                    </x-forms.label>
                                    <select class="form-control select-picker" name="category_id" id="category_id"
                                        data-live-search="true">
                                        <option value="100">--</option>
                                        @foreach ($categories as $category)
                                            @if(isset($apporteurs) && $apporteurs->category_id == $category->id)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->category_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <div class="col-md-4">
                                    <x-forms.label class="mt-3" fieldId="category"
                                        :fieldLabel="__('modules.client.clientCategory')">
                                    </x-forms.label>
                                    <x-forms.input-group>
                                        <select class="form-control select-picker" name="category_id" id="category_id"
                                            data-live-search="true">
                                            <option value="100">--</option>
                                            @foreach ($categories as $category)
                                                <option @selected(isset($lead) && $lead->category_id == $category->id) value="{{ $category->id }}">
                                            {{ $category->category_name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($addClientCategoryPermission == 'all')
                                            <x-slot name="append">
                                                <button id="addClientCategory" type="button"
                                                    class="btn btn-outline-secondary border-grey"
                                                    data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.client.clientCategory') }}">
                                                    @lang('app.add')</button>
                                            </x-slot>
                                        @endif
                                    </x-forms.input-group>
                                </div>
                            @endif
                            @if(!in_array('apporteurs', user_roles()))
                            <div class="col-md-4" style="display:none;">
                                <x-forms.label class="mt-3" fieldId="sub_category_id"
                                    :fieldLabel="__('modules.client.clientSubCategory')"></x-forms.label>
                                <x-forms.input-group>
                                    <!--<select class="form-control select-picker" name="sub_category_id" id="sub_category_id"
                                        data-live-search="true">
                                        <option value="">--</option>
                                    </select>-->
                                    <input type="hidden" name="sub_category_id" id="sub_category_id">
                                    @if ($addClientSubCategoryPermission == 'all')
                                        <x-slot name="append">
                                            <button id="addClientSubCategory" type="button"
                                                class="btn btn-outline-secondary border-grey"
                                                data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.client.clientSubCategory') }}"
                                                >@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>
                            @endif
                            <div class="col-md-4" id="numadhe" style="display:none;">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numadh1"
                                        :fieldLabel="__('Numéro d\'adhésion CGA')" fieldName="numadh1"
                                        :fieldPlaceholder="__('')" :fieldValue="$formattedNumber1">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-4" id="numcabinet" style="display:none;">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numcga1"
                                        :fieldLabel="__('Numéro Cabinet')" fieldName="numcga1"
                                        :fieldPlaceholder="__('')" :fieldValue="$formattedNumber">
                                    </x-forms.text>
                                </div>
                            </div>
                            <input type="hidden" name="numadh" id="numadh">
                            <input type="hidden" name="numcga" id="numcga">
                            <div class="col-md-4">
                                <x-forms.email fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                                    :popover="__('modules.client.emailNote')" :fieldPlaceholder="__('placeholders.email')"
                                    :fieldValue="$lead->client_email ?? ''">
                                </x-forms.email>
                            </div>
                            <div class="col-md-4">
                                <x-forms.label class="mt-3" fieldId="password" :fieldLabel="__('app.password')"
                                    :popover="__('messages.requiredForLogin')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <input type="password" name="password" id="password" class="form-control height-35 f-14">
                                    <x-slot name="preappend">
                                        <button type="button" data-toggle="tooltip"
                                            data-original-title="@lang('app.viewPassword')"
                                            class="btn btn-outline-secondary border-grey height-35 toggle-password"><i
                                                class="fa fa-eye"></i></button>
                                    </x-slot>
                                    <x-slot name="append">
                                        <button id="random_password" type="button" data-toggle="tooltip"
                                            data-original-title="@lang('modules.client.generateRandomPassword')"
                                            class="btn btn-outline-secondary border-grey height-35"><i
                                                class="fa fa-random"></i></button>
                                    </x-slot>
                                </x-forms.input-group>
                                <small class="form-text text-muted">@lang('placeholders.password')</small>
                            </div>
                            <div class="col-md-4" id="name_ese">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="company_name"
                                        :fieldLabel="__('Nom de l\'entreprise')" fieldName="company_name" fieldRequired="true"
                                        :fieldPlaceholder="__('placeholders.company')" :fieldValue="$lead->company_name ?? ''">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="company_name_com"
                                        :fieldLabel="__('Nom commercial')" fieldName="company_name_com"
                                        :fieldPlaceholder="__('Nom commercial')" :fieldValue="$lead->company_name_com ?? ''">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <x-forms.select fieldId="gender" :fieldLabel="__('modules.employees.gender')"
                                    fieldName="gender">
                                    <option value="male">@lang('app.male')</option>
                                    <option value="female">@lang('app.female')</option>
                                    <option value="others">@lang('app.others')</option>
                                </x-forms.select>
                            </div>
                            <div class="col-md-2">
                                <x-forms.select fieldId="salutation" fieldName="salutation"
                                    :fieldLabel="__('modules.client.salutation')">
                                    <option value="">--</option>
                                    @foreach ($salutations as $salutation)
                                        <option value="{{ $salutation->value }}" @selected(isset($lead) && $salutation == $lead->salutation)>{{ $salutation->label() }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                            <div class="col-md-4">
                                <x-forms.text fieldId="name" :fieldLabel="__('Nom du dirigeant')" fieldName="name"
                                    fieldRequired="true" :fieldPlaceholder="__('Nom du dirigeant')"
                                    :fieldValue="$lead->client_name ?? ''"></x-forms.text>
                            </div>
                            <div class="col-md-4">
                                <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country"
                                    search="true">
                                    <option data-tokens="CIV" data-phonecode="225" data-iso="CI" data-content="<span class='flag-icon flag-icon-ci flag-icon-squared'></span> Côte d'Ivoire" value="53" @selected(isset($lead) && $lead->country == 'Cote D\'Ivoire')>Côte d'Ivoire</option>
                                    @foreach ($countries as $item)
                                        @if($item->iso != 'CI')
                                            <option data-tokens="{{ $item->iso3 }}" data-phonecode = "{{$item->phonecode}}"
                                                data-iso="{{ $item->iso }}" data-content="<span class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span> {{ $item->nicename }}"
                                                @selected(isset($lead) && $item->nicename == $lead->country)
                                                value="{{ $item->id }}">{{ $item->nicename }}</option>
                                        @endif
                                    @endforeach
                                </x-forms.select>
                            </div>
                            <div class="col-md-4">
                                <x-forms.label class="my-3" fieldId="mobile"
                                    :fieldLabel="__('app.mobile')"></x-forms.label>
                                <x-forms.input-group style="margin-top:-4px">
                                    <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                        search="true">
                                        <option data-tokens="Côte d'Ivoire" data-country-iso="CI"
                                                data-content="<span class='flag-icon flag-icon-ci flag-icon-squared'></span> +225"
                                                @selected(isset($lead) && $lead->country == 'Cote D\'Ivoire')
                                                value="+225">+225</option>
                                        @foreach ($countries as $item)
                                            @if($item->iso != 'CI')
                                                <option data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                                        data-content="{{$item->flagSpanCountryCode()}}"
                                                        @selected(isset($lead) && $item->nicename == $lead->country)
                                                        value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </x-forms.select>
                                    <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                        name="mobile" id="mobile" value="{{$lead->mobile ?? ''}}">
                                </x-forms.input-group>
                            </div>
                            <div class="col-md-4">
                                <x-forms.label class="my-3" fieldId="whatsapp"
                                    :fieldLabel="__('Numéro WhatsApp')"></x-forms.label>
                                <x-forms.input-group style="margin-top:-4px">
                                    <x-forms.select fieldId="whatsapp_phoneCode" fieldName="whatsapp_phoneCode"
                                        search="true">
                                        <option data-tokens="Côte d'Ivoire" data-country-iso="CI"
                                                data-content="<span class='flag-icon flag-icon-ci flag-icon-squared'></span> +225"
                                                @selected(isset($lead) && $lead->country == 'Cote D\'Ivoire')
                                                value="+225">+225</option>
                                        @foreach ($countries as $item)
                                            @if($item->iso != 'CI')
                                                <option data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                                        data-content="{{$item->flagSpanCountryCode()}}"
                                                        @selected(isset($lead) && $item->nicename == $lead->country)
                                                        value="{{ $item->phonecode }}">{{ $item->phonecode }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </x-forms.select>
                                    <input type="tel" class="form-control height-35 f-14" placeholder="Numéro WhatsApp"
                                        name="whatsapp" id="whatsapp" value="{{$lead->whatsapp ?? ''}}">
                                </x-forms.input-group>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('modules.profile.profilePicture')" fieldName="image" fieldId="image"
                            fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>
                    {{--
                    <div class="col-md-3">
                        <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.changeLanguage')"
                            fieldName="locale" search="true">
                            @foreach ($languages as $language)
                                <option {{ user()->locale == $language->language_code ? 'selected' : '' }}
                                data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : $language->flag_code }} flag-icon-squared'></span> {{ $language->language_name }}"
                                value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    --}}
                    @if(!in_array('apporteurs', user_roles()))
                    <div class="col-md-3">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr">@lang('modules.client.clientCanLogin')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.yes')" fieldName="login"
                                    fieldValue="enable">
                                </x-forms.radio>
                                <x-forms.radio fieldId="login-no" :fieldLabel="__('app.no')" fieldValue="disable"
                                    fieldName="login" checked="true"></x-forms.radio>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr">@lang('modules.emailSettings.emailNotifications')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="notification-yes" :fieldLabel="__('app.yes')" fieldValue="yes"
                                    fieldName="sendMail" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="notification-no" :fieldLabel="__('app.no')" fieldValue="no"
                                    fieldName="sendMail">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-top-grey">
                    @lang('modules.client.companyDetails')</h4>
                <div class="row p-20">
                    
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
                        <label class="f-14 text-dark-grey mb-12" data-label="" for="gst_number">Numéro Compte Contribuable</label>  
                        <input type="text" class="form-control height-35 f-14" id="gst_number" name="gst_number" maxlength="8" placeholder="Numéro Compte Contribuable">
                    </div>

                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numrccm"
                            :fieldLabel="__('Numéro IDU')" fieldName="numrccm"
                            :fieldPlaceholder="__('Numéro IDU')">
                        </x-forms.text>
                    </div>

                    <div class="col-md-3">
                        <x-forms.label class="mb-3 mt3 mt-lg-0 mt-md-0" fieldId="regime"
                            :fieldLabel="__('Régime fiscal')">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select id="regime" name="regime" class="form-control select-picker" data-live-search="true">
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
                            <select id="imp_centre" name="imp_centre" class="form-control select-picker" data-live-search="true">
                                <option value="">--</option>
                                <option value="II Plateaux III">II Plateaux III</option>
                                <option value="II Plateaux Djibi">II Plateaux Djibi</option>
                                <option value="II Plateaux I">II Plateaux I</option>
                                <option value="II Plateaux II">II Plateaux II</option>
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
                        <x-forms.text class="mb-3 mt-2 mt-lg-0 mt-md-0" fieldId="acti_prin"
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
                        <x-forms.number class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="montcapit"
                            :fieldLabel="__('Montant du capital')" fieldName="montcapit"
                            :fieldPlaceholder="__('Montant du capital')">
                        </x-forms.number>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text fieldId="office"  class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.client.officePhoneNumber')"
                            fieldName="office" :fieldPlaceholder="__('placeholders.mobileWithPlus')" :fieldValue="$lead->office ?? ''">
                        </x-forms.text>
                    </div>
                    <div class="col-md-4">
                        <x-forms.text fieldId="city"  class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.stripeCustomerAddress.city')"
                            fieldName="city" :fieldPlaceholder="__('placeholders.city')" :fieldValue="$lead->city ?? ''">
                        </x-forms.text>
                    </div>
                    <div class="col-md-4">
                        <x-forms.text fieldId="state"  class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('Sigle')"
                            fieldName="state" :fieldPlaceholder="__('Sigle')" :fieldValue="$lead->state ?? ''">
                        </x-forms.text>
                    </div> 
                    <div class="col-md-4">   
                        <x-forms.text fieldId="postalCode"  class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.stripeCustomerAddress.postalCode')"
                            fieldName="postal_code" :fieldPlaceholder="__('placeholders.postalCode')" 
                            :fieldValue="$lead->postal_code ?? ''">
                        </x-forms.text>
                    </div>

                    @if ($addPermission == 'all')
                        <div class="col-lg-6 col-md-6">
                            <x-forms.select fieldId="added_by" :fieldLabel="__('app.added').' '.__('app.by')"
                                fieldName="added_by">
                                <option value="">--</option>
                                @foreach ($employees as $item)
                                    <x-user-option :user="$item" :selected="user()->id == $item->id" />
                                @endforeach
                            </x-forms.select>
                        </div>
                    @endif
                    <div class="col-md-12">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                :fieldLabel="__('Localisation (Adresse)')" fieldName="address"
                                fieldId="address" :fieldPlaceholder="__('placeholders.address')"
                                :fieldValue="$lead->address ?? ''">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.shippingAddress')"
                                fieldName="shipping_address" fieldId="shipping_address"
                                :fieldPlaceholder="__('placeholders.address')" :fieldValue="$lead->address ?? ''">
                            </x-forms.textarea>
                        </div>
                    </div>

                    @if (function_exists('sms_setting') && sms_setting()->telegram_status)
                        <div class="col-md-6">
                            <x-forms.number fieldName="telegram_user_id" fieldId="telegram_user_id"
                                fieldLabel="<i class='fab fa-telegram'></i> {{ __('sms::modules.telegramUserId') }}"
                                :popover="__('sms::modules.userIdInfo')" />
                            <p class="text-bold text-danger">
                                @lang('sms::modules.telegramBotNameInfo')
                            </p>
                            <p class="text-bold"><span id="telegram-link-text">https://t.me/{{ sms_setting()->telegram_bot_name }}</span>
                                <a href="javascript:;" class="btn-copy btn-secondary f-12 rounded p-1 py-2 ml-1"
                                    data-clipboard-target="#telegram-link-text">
                                    <i class="fa fa-copy mx-1"></i>@lang('app.copy')</a>
                                <a href="https://t.me/{{ sms_setting()->telegram_bot_name }}" target="_blank" class="btn-secondary f-12 rounded p-1 py-2 ml-1">
                                    <i class="fa fa-copy mx-1"></i>@lang('app.openInNewTab')</a>
                            </p>
                        </div>
                    @endif

                    @if ($addClientNotePermission == 'all' || $addClientNotePermission == 'added' || $addClientNotePermission == 'both')
                    <div class="col-md-12">
                        <div class="form-group my-3">
                            <x-forms.label class="my-3" fieldId="note" :fieldLabel="__('app.note')">
                            </x-forms.label>
                            <div id="note"></div>
                            <textarea name="note" id="note-text" class="d-none"></textarea>
                        </div>
                    </div>
                    @endif

                    <div class="col-lg-12">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2"
                                               :fieldLabel="__('modules.contracts.companyLogo')" fieldName="company_logo"
                                               :fieldValue="(company()->logo_url)" fieldId="company_logo" :popover="__('messages.fileFormat.ImageFile')"/>
                    </div>

                    @includeIf('einvoice::form.client-create')
                    <input type ="hidden" name="add_more" value="false" id="add_more" />

                </div>

                <x-forms.custom-field :fields="$fields"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="save-client-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-secondary class="mr-3" id="save-more-client-form" icon="check-double">@lang('app.saveAddMore')
                    </x-forms.button-secondary>
                    <x-forms.button-cancel :link="route('clients.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

@if (function_exists('sms_setting') && sms_setting()->telegram_status)
    <script src="{{ asset('vendor/jquery/clipboard.min.js') }}"></script>
@endif
<script>
    document.getElementById('numcga1').disabled = true;
    document.getElementById('numadh1').disabled = true;

    var add_client_note_permission = "{{  $addClientNotePermission }}";

    $(document).ready(function() {
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        
        $('#formjurid').change(function(e) {

            let formjuridiqueId = $(this).val();
            const company_name = document.getElementById('company_name');
            var company_name_com = document.getElementById('company_name_com').value; 

            if(formjuridiqueId === 'ENTREPRISE INDIVIDUELLE'){
                $('#name_ese').hide();
                //$('#company_name').val($('#company_name_com').val());
                company_name.value = company_name_com;
            }else{
                $('#name_ese').show();
                //$('#numcabinet').hide(); 
            }
        });

        $('#country').change(function(){
            var phonecode = $(this).find(':selected').data('phonecode');
            var iso = $(this).find(':selected').data('iso');

            $('#country_phonecode').find('option').each(function() {
                if ($(this).data('country-iso') === iso) {
                    $(this).val(phonecode);
                    $(this).prop('selected', true); // Set the option as selected
                }
            });

            $('#whatsapp_phoneCode').find('option').each(function() {
                if ($(this).data('country-iso') === iso) {
                    $(this).val(phonecode);
                    $(this).prop('selected', true); // Set the option as selected
                }
            });

            $('.select-picker').selectpicker('refresh');
        });

        if(add_client_note_permission == 'all' || add_client_note_permission == 'added' || add_client_note_permission == 'both')
        {
            quillImageLoad('#note');
        }

        $('#category_id').change(function(e) {

            let categoryId = $(this).val();

            if (categoryId === '') {
                $('#sub_category_id').html('<option value="">--</option>');
                $('#sub_category_id').selectpicker('refresh');
                return; // Stop further execution when no category is selected
            }

            var url = "{{ route('get_client_sub_categories', ':id') }}";
            url = url.replace(':id', categoryId);
            if (categoryId === '11') {
                $('#numadhe').hide();
                $('#numcabinet').show();
                document.getElementById('numcga').value = document.getElementById('numcga1').value;
                document.getElementById('numadh').value = null;
                document.getElementById('sub_category_id').value = 1;
            }else{
                $('#numadhe').show();
                $('#numcabinet').hide();
                document.getElementById('numcga').value = document.getElementById('numcga1').value;
                document.getElementById('numadh').value = document.getElementById('numadh1').value;
                document.getElementById('sub_category_id').value = 2;
            }


            $.easyAjax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData = [];
                        rData = response.data;
                        $.each(rData, function(index, value) {
                            var selectData = '';
                            selectData = '<option value="' + value.id + '">' + value
                                .category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#sub_category_id').html('<option value="">--</option>' +
                            options);
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            })

        });

        /*$('#sub_category_id').change(function(e) {

            let categoryId = $(this).val();

            if (categoryId === '1') {
                $('#numadhe').hide();
                $('#numcabinet').show();
                document.getElementById('numcga').value = document.getElementById('numcga1').value;
                document.getElementById('numadh').value = null;
            }else{
                $('#numadhe').show();
                $('#numcabinet').hide();
                document.getElementById('numcga').value = document.getElementById('numcga1').value;
                document.getElementById('numadh').value = document.getElementById('numadh1').value ;
            }

        });*/

        $('#save-more-client-form').click(function () {

            $('#add_more').val(true);

            if(add_client_note_permission == 'all' || add_client_note_permission == 'added' || add_client_note_permission == 'both')
            {
                var note = document.getElementById('note').children[0].innerHTML;
                document.getElementById('note-text').value = note;
            }

            const url = "{{ route('clients.store') }}?add_more=true";
            // var data = $('#save-client-data-form').serialize() + '&add_more=true';
            var data = $('#save-client-data-form').serialize();

            // console.log(data);
            saveClient(data, url, "#save-more-client-form");

        });

        $('#save-client-form').click(function() {
            if(add_client_note_permission == 'all' || add_client_note_permission == 'added' || add_client_note_permission == 'both')
            {
                var note = document.getElementById('note').children[0].innerHTML;
                document.getElementById('note-text').value = note;
            }

            const url = "{{ route('clients.store') }}";
            var data = $('#save-client-data-form').serialize();

            saveClient(data, url, "#save-client-form");

        });

        function saveClient(data, url, buttonSelector) {
            $.easyAjax({
                url: url,
                container: '#save-client-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: buttonSelector,
                file: true,
                data: data,
                success: function(response) {
                    if (response.status == 'success') {
                        if ($(MODAL_XL).hasClass('show')) {

                            $(MODAL_XL).modal('hide');
                            window.location.reload();
                        } else if(typeof response.redirectUrl !== 'undefined'){
                            window.location.href = response.redirectUrl;
                        }
                        else if(response.add_more == true) {

                            var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());
                            if(right_modal_content.length) {

                                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                                $('#add_more').val(false);
                            }
                            else {

                                $('.content-wrapper').html(response.html.html);
                                init('.content-wrapper');
                                $('#add_more').val(false);
                            }
                        }

                        if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
                            showTable();
                        }
                    }
                }
            });
        }

        $('#random_password').click(function() {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('#addClientCategory').click(function() {
            const url = "{{ route('clientCategory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })
        $('#addClientSubCategory').click(function() {
            const url = "{{ route('clientSubCategory.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        init(RIGHT_MODAL);
    });

    @if (function_exists('sms_setting') && sms_setting()->telegram_status)
        var clipboard = new ClipboardJS('.btn-copy');

        clipboard.on('success', function(e) {
            Swal.fire({
                icon: 'success',
                text: '@lang("app.urlCopied")',
                toast: true,
                position: 'top-end',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
            })
        });
    @endif
</script>
