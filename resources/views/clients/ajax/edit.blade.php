@php
$addClientCategoryPermission = user()->permission('manage_client_category');
$addClientSubCategoryPermission = user()->permission('manage_client_subcategory');
use Illuminate\Support\Facades\DB;

//echo "Nouveau numéro ADH : " . $formattedNumber;
@endphp

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal  border-bottom-grey">
                    @lang('modules.employees.accountDetails')</h4>

                <div class="row p-20">
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-md-4">                        
                                <x-forms.label class="mt-3" fieldId="formjurid"
                                    :fieldLabel="__('Forme juridique')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select id="formjurid" name="formjurid" class="form-control select-picker" data-live-search="true">
                                        <option value="{{$client->clientDetails->formjurid}}">{{$client->clientDetails->formjurid}}</option>
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
                            <div class="col-md-4">
                                <x-forms.label class="mt-3" fieldId="category"
                                    :fieldLabel="__('modules.client.clientCategory')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <select class="form-control select-picker" name="category_id" id="category_id"
                                        data-live-search="true">
                                        @foreach($categories as $category)
                                            <option @selected($client->clientDetails->category_id == $category->id) value="{{ $category->id }}">
                                                {{ $category->category_name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($addClientCategoryPermission == 'all' || $addClientCategoryPermission == 'added' || $addClientCategoryPermission == 'both')
                                        <x-slot name="append">
                                            <button id="addClientCategory" type="button"
                                                class="btn btn-outline-secondary border-grey"
                                                data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.client.clientCategory') }}">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>
                            @if(!in_array('apporteurs', user_roles()))
                            <div class="col-md-4" style="display:none;">
                                <x-forms.label class="mt-3" fieldId="sub_category_id"
                                    :fieldLabel="__('modules.client.clientSubCategory')"></x-forms.label>
                                <x-forms.input-group>
                                    <!--
                                        <select class="form-control select-picker" name="sub_category_id" id="sub_category_id"
                                            data-live-search="true">
                                            <option value="">--</option>
                                            @forelse($subcategories as $subcategory)
                                                <option  @selected($client->clientDetails->sub_category_id == $subcategory->id) value="{{ $subcategory->id }}">
                                                    {{ $subcategory->category_name }}</option>
                                            @empty
                                                <option value="">@lang('messages.noCategoryAdded')</option>
                                            @endforelse
                                        </select>
                                    -->
                                    <input type="hidden" name="sub_category_id" id="sub_category_id" value="{{ $subcategory->id }}">
                                    @if ($addClientSubCategoryPermission == 'all' || $addClientSubCategoryPermission == 'added' || $addClientSubCategoryPermission == 'both')
                                        <x-slot name="append">
                                            <button id="addClientSubCategory" type="button"
                                                class="btn btn-outline-secondary border-grey"
                                                data-toggle="tooltip" data-original-title="{{ __('app.add').' '.__('modules.client.clientSubCategory') }}">@lang('app.add')</button>
                                        </x-slot>
                                    @endif
                                </x-forms.input-group>
                            </div>
                            @endif
                            <div class="col-md-4" id="numadhe" style="display:none;">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numadh1"
                                        :fieldLabel="__('Numéro d\'adhésion CGA')" fieldName="numadh1"
                                        :fieldPlaceholder="__('')" :fieldValue="$client->clientDetails->numadh">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-4" id="numcabinet" style="display:none;">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numcga1"
                                        :fieldLabel="__('Numéro Cabinet')" fieldName="numcga1"
                                        :fieldPlaceholder="__('')" :fieldValue="$client->clientDetails->numcga">
                                    </x-forms.text>
                                </div>
                            </div>
                            <input type="hidden" name="numadh" id="numadh">
                            <input type="hidden" name="numcga" id="numcga">
                            <div class="col-lg-4 col-md-6">
                                <x-forms.email fieldId="email" :fieldLabel="__('app.email')" fieldName="email"
                                    :fieldPlaceholder="__('placeholders.email')"
                                    :fieldValue="$client->email">
                                </x-forms.email>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.label class="mt-3" fieldId="password" :fieldLabel="__('app.password')"
                                    :popover="__('messages.requiredForLogin')">
                                </x-forms.label>
                                <x-forms.input-group>
                                    <input type="password" name="password" id="password" autocomplete="off"
                                        class="form-control height-35 f-14">
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
                                <small class="form-text text-muted">@lang('modules.client.passwordUpdateNote')</small>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="company_name"
                                        :fieldLabel="__('modules.client.companyName')" fieldName="company_name" fieldRequired="true"
                                        :fieldPlaceholder="__('placeholders.company')" :fieldValue="$client->clientDetails->company_name">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group my-3">
                                    <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="company_name_com"
                                        :fieldLabel="__('Nom commercial')" fieldName="company_name_com"
                                        :fieldPlaceholder="__('Nom commercial')" :fieldValue="$client->clientDetails->company_name_com">
                                    </x-forms.text>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <x-forms.select fieldId="gender" :fieldLabel="__('modules.employees.gender')"
                                    fieldName="gender">
                                    <option value="male" {{ $client->gender == 'male' ? 'selected' : '' }}>@lang('app.male')
                                    </option>
                                    <option value="female" {{ $client->gender == 'female' ? 'selected' : '' }}>
                                        @lang('app.female')</option>
                                    <option value="others" {{ $client->gender == 'others' ? 'selected' : '' }}>
                                        @lang('app.others')</option>
                                </x-forms.select>
                            </div>
                            <div class="col-md-2">
                                <x-forms.select fieldId="salutation" fieldName="salutation"
                                    :fieldLabel="__('modules.client.salutation')">
                                    <option value="">--</option>
                                    @foreach ($salutations as $salutation)
                                        <option value="{{ $salutation->value }}" @selected($client->salutation == $salutation)>{{ $salutation->label() }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <x-forms.text fieldId="name" :fieldLabel="__('Nom du dirigeant')" fieldName="name"
                                    fieldRequired="true" :fieldPlaceholder="__('placeholders.name')"
                                    :fieldValue="$client->name">
                                </x-forms.text>
                            </div>
                            <div class="col-md-4">
                                <x-forms.select fieldId="country" :fieldLabel="__('app.country')" fieldName="country"
                                search="true">
                                    <option value="">--</option>
                                    @foreach ($countries as $item)
                                        <option @selected($client->country_id == $item->id) data-mobile="{{ $client->mobile }}" data-tokens="{{ $item->iso3 }}" data-phonecode="{{ $item->phonecode }}" data-content="<span
                                            class='flag-icon flag-icon-{{ strtolower($item->iso) }} flag-icon-squared'></span>
                                            {{ $item->nicename }}" data-iso="{{ $item->iso }}" value="{{ $item->id }}">{{ $item->nicename }}</option>
                                    @endforeach
                                </x-forms.select>
                            </div>
                            <div class="col-md-4">
                                <x-forms.label class="my-3" fieldId="mobile"
                                    :fieldLabel="__('app.mobile')"></x-forms.label>
                                <x-forms.input-group style="margin-top:-4px">
                                    <x-forms.select fieldId="country_phonecode" fieldName="country_phonecode"
                                        search="true">
                                        @foreach ($countries as $item)
                                            <option @selected($client->country_phonecode == $item->phonecode && !is_null($item->numcode))
                                                    data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                                    data-content="{{$item->flagSpanCountryCode()}}"
                                                    value="{{ $item->phonecode }}">
                                            </option>
                                        @endforeach
                                    </x-forms.select>
                                    <input type="tel" class="form-control height-35 f-14" placeholder="@lang('placeholders.mobile')"
                                        name="mobile" id="mobile" value="{{ $client->mobile }}">
                                </x-forms.input-group>
                            </div>
                            <div class="col-md-4">
                                <x-forms.label class="my-3" fieldId="whatsapp"
                                    :fieldLabel="__('Numéro WhatsApp')"></x-forms.label>
                                <x-forms.input-group style="margin-top:-4px">
                                    <x-forms.select fieldId="whatsapp_phoneCode" fieldName="whatsapp_phoneCode"
                                        search="true">
                                        @foreach ($countries as $item)
                                            <option @selected($client->whatsapp_phoneCode == $item->phonecode && !is_null($item->numcode))
                                                    data-tokens="{{ $item->name }}" data-country-iso="{{ $item->iso }}"
                                                    data-content="{{$item->flagSpanCountryCode()}}"
                                                    value="{{ $item->phonecode }}">
                                            </option>
                                        @endforeach
                                    </x-forms.select>
                                    <input type="tel" class="form-control height-35 f-14" placeholder="Numéro WhatsApp"
                                        name="whatsapp" id="whatsapp" value="{{ $client->whatsapp }}">
                                </x-forms.input-group>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2 cropper"
                            :fieldLabel="__('modules.profile.profilePicture')"
                            :fieldValue="$client->image_url" fieldName="image"
                            fieldId="image" fieldHeight="119" :popover="__('messages.fileFormat.ImageFile')" />
                    </div>

                    {{--<div class="col-md-3">
                        <x-forms.select fieldId="locale" :fieldLabel="__('modules.accountSettings.changeLanguage')"
                            fieldName="locale" search="true">
                            @foreach ($languages as $language)
                                <option @selected($client->locale == $language->language_code)
                                data-content="<span class='flag-icon flag-icon-{{ ($language->flag_code == 'en') ? 'gb' : $language->flag_code }} flag-icon-squared'></span> {{ $language->language_name }}"
                                value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>--}}
                    @if(!in_array('apporteurs', user_roles()))
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr">@lang('modules.client.clientCanLogin')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="login-yes" :fieldLabel="__('app.yes')" fieldName="login"
                                    fieldValue="enable" :checked="($client->login == 'enable') ? 'checked' : ''">
                                </x-forms.radio>
                                <x-forms.radio fieldId="login-no" :fieldLabel="__('app.no')" fieldValue="disable"
                                    fieldName="login" :checked="($client->login == 'disable') ? 'checked' : ''">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3"
                                for="usr">@lang('modules.emailSettings.emailNotifications')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="notification-yes" :fieldLabel="__('app.yes')" fieldValue="yes"
                                    fieldName="sendMail" checked="($client->email_notifications) ? 'checked' : ''">
                                </x-forms.radio>
                                <x-forms.radio fieldId="notification-no" :fieldLabel="__('app.no')" fieldValue="no"
                                    fieldName="sendMail" :checked="(!$client->email_notifications) ? 'checked' : ''">
                                </x-forms.radio>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group my-3">
                            <label class="f-14 text-dark-grey mb-12 w-100 mt-3" for="usr">@lang('app.status')</label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="status-active" :fieldLabel="__('app.active')"
                                    fieldValue="active" fieldName="status"
                                    checked="($client->status == 'active') ? 'checked' : ''">
                                </x-forms.radio>
                                <x-forms.radio fieldId="status-inactive" :fieldLabel="__('app.inactive')"
                                    fieldValue="deactive" fieldName="status"
                                    :checked="($client->status == 'deactive') ? 'checked' : ''">
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
                            :fieldPlaceholder="__('placeholders.website')" :fieldValue="$client->clientDetails->website">
                        </x-forms.text>
                    </div>

                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="tax_name"
                            :fieldLabel="__('Numéro RCCM')" fieldName="tax_name"
                            :fieldPlaceholder="__('Numéro RCCM')" :fieldValue="$client->clientDetails->tax_name">
                        </x-forms.text>
                    </div>

                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="gst_number"
                            :fieldLabel="__('Numéro Compte Contribuable')" fieldName="gst_number"
                            :fieldPlaceholder="__('Numéro Compte Contribuable')" :fieldValue="$client->clientDetails->gst_number">
                        </x-forms.text>
                    </div>

                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="numrccm"
                            :fieldLabel="__('Numéro IDU')" fieldName="numrccm"
                            :fieldPlaceholder="__('Numéro IDU')" :fieldValue="$client->clientDetails->numrccm">
                        </x-forms.text>
                    </div>

                   
                    
                    <div class="col-md-3">
                        <x-forms.label class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="regime"
                            :fieldLabel="__('Régime fiscal')">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select id="regime" name="regime" class="form-control select-picker" data-live-search="true">
                                <option value="{{$client->clientDetails->regime}}">{{$client->clientDetails->regime}}</option>
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
                                <option value="{{$client->clientDetails->imp_centre}}">{{$client->clientDetails->imp_centre}}</option>
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
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="acti_prin"
                            :fieldLabel="__('Activités principales')" fieldName="acti_prin"
                            :fieldPlaceholder="__('Activités principales')" :fieldValue="$client->clientDetails->acti_prin">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="section"
                            :fieldLabel="__('Section')" fieldName="section"
                            :fieldPlaceholder="__('Section')" :fieldValue="$client->clientDetails->section">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="parcelle"
                            :fieldLabel="__('Parcelle')" fieldName="parcelle"
                            :fieldPlaceholder="__('Parcelle')" :fieldValue="$client->clientDetails->parcelle">
                        </x-forms.text>
                    </div>
                    
                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="codeacti"
                            :fieldLabel="__('Code activité')" fieldName="codeacti"
                            :fieldPlaceholder="__('Code activité')" :fieldValue="$client->clientDetails->codeacti">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text class="mb-3 mt-3 mt-lg-0 mt-md-0" fieldId="montcapit"
                            :fieldLabel="__('Montant du capital')" fieldName="montcapit"
                            :fieldPlaceholder="__('Montant du capital')" :fieldValue="$client->clientDetails->montcapit">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text fieldId="office" class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.client.officePhoneNumber')"
                            fieldName="office" :fieldPlaceholder="__('placeholders.mobileWithPlus')" :fieldValue="$client->clientDetails->office">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text fieldId="city" class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.stripeCustomerAddress.city')"
                            fieldName="city" :fieldPlaceholder="__('placeholders.city')" :fieldValue="$client->clientDetails->city">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text fieldId="state" class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('Sigle')"
                            fieldName="state" :fieldPlaceholder="__('Sigle')" :fieldValue="$client->clientDetails->state">
                        </x-forms.text>
                    </div>
                    <div class="col-md-3">
                        <x-forms.text fieldId="postalCode" class="mb-3 mt-3 mt-lg-0 mt-md-0" :fieldLabel="__('modules.stripeCustomerAddress.postalCode')"
                            fieldName="postal_code" :fieldPlaceholder="__('placeholders.postalCode')"
                            :fieldValue="$client->clientDetails->postal_code">
                        </x-forms.text>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2"
                                :fieldLabel="__('Localisation (Adresse)')" fieldName="address"
                                fieldId="address" :fieldPlaceholder="__('placeholders.address')"
                                :fieldValue="$client->clientDetails->address">
                            </x-forms.textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group my-3">
                            <x-forms.textarea class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="__('app.shippingAddress')"
                                :fieldValue="$client->clientDetails->shipping_address" fieldName="shipping_address"
                                fieldId="shipping_address" :fieldPlaceholder="__('placeholders.address')">
                            </x-forms.textarea>
                        </div>
                    </div>

                    @if ($editPermission == 'all')
                        <div class="col-lg-3 col-md-6">
                            <x-forms.select fieldId="added_by" :fieldLabel="__('app.added').' '.__('app.by')"
                                fieldName="added_by">
                                <option value="">--</option>
                                @foreach ($employees as $item)
                                    @if($item->status == 'active' || $client->clientDetails->added_by == $item->id)
                                        <x-user-option :user="$item" :selected="$client->clientDetails->added_by == $item->id" />
                                    @endif
                                @endforeach
                            </x-forms.select>
                        </div>
                    @endif

                    @if (function_exists('sms_setting') && sms_setting()->telegram_status)
                        <div class="col-md-6">
                            <x-forms.number fieldName="telegram_user_id" fieldId="telegram_user_id"
                                fieldLabel="<i class='fab fa-telegram'></i> {{ __('sms::modules.telegramUserId') }}"
                                :fieldValue="$client->telegram_user_id" :popover="__('sms::modules.userIdInfo')" />
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

                    <div class="col-lg-12">
                        <x-forms.file allowedFileExtensions="png jpg jpeg svg bmp" class="mr-0 mr-lg-2 mr-md-2"
                                               :fieldLabel="__('modules.contracts.companyLogo')" fieldName="company_logo"
                                               :fieldValue=" ($client->clientDetails->company_logo ? $client->clientDetails->image_url : null)" fieldId="company_logo" :popover="__('messages.fileFormat.ImageFile')"/>
                    </div>
                </div>
                @includeIf('einvoice::form.client-edit')

                <x-forms.custom-field :fields="$fields" :model="$clientDetail"></x-forms.custom-field>

                <x-form-actions>
                    <x-forms.button-primary id="save-form" class="mr-3" icon="check">@lang('app.save')
                    </x-forms.button-primary>
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
    $(document).ready(function() {

        $('#random_password').click(function() {
            const randPassword = Math.random().toString(36).substr(2, 8);

            $('#password').val(randPassword);
        });

        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        function updatePhoneCode() {
            var selectedCountry = $('#country').find(':selected');
            var phonecode = selectedCountry.data('phonecode');
            var iso = selectedCountry.data('iso');

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
        }
        updatePhoneCode();

        $('#country').change(function(){
            updatePhoneCode();
            $('.select-picker').selectpicker('refresh');
        });


        // Function to load subcategories based on selected category
        function loadSubCategories(categoryId, selectedSubCategoryId = null) {

            if (categoryId === '') {
                $('#sub_category_id').html('<option value="">--</option>');
                $('#sub_category_id').selectpicker('refresh');
                return; // Stop further execution if no category is selected
            }

            var url = "{{ route('get_client_sub_categories', ':id') }}";
            url = url.replace(':id', categoryId);

            if (categoryId === '1') {
                $('#numadhe').hide();
                $('#numcabinet').hide();
            }else{
                $('#numadhe').show();
                $('#numcabinet').hide();
                document.getElementById('numcga').value = document.getElementById('numcga1').value;
                document.getElementById('numadh').value = document.getElementById('numadh1').value ;
            }

            $.easyAjax({
                url: url,
                type: "GET",
                success: function(response) {
                    if (response.status == 'success') {
                        var options = [];
                        var rData = response.data;

                        $.each(rData, function(index, value) {
                            var isSelected = selectedSubCategoryId && selectedSubCategoryId == value.id ? 'selected' : '';
                            var selectData = '<option value="' + value.id + '" ' + isSelected + '>' + value.category_name + '</option>';
                            options.push(selectData);
                        });

                        $('#sub_category_id').html('<option value="">--</option>' + options);
                        $('#sub_category_id').selectpicker('refresh');
                    }
                }
            });
        }

        // On change of category, fetch subcategories
        $('#category_id').change(function() {
            var categoryId = $(this).val();
            loadSubCategories(categoryId);
        });
        $('#sub_category_id').change(function(e) {

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

        });

        // Pre-load subcategories in the edit form
        var selectedCategoryId = "{{ $client->clientDetails->category_id }}";
        var selectedSubCategoryId = "{{ $client->clientDetails->sub_category_id }}";

        loadSubCategories(selectedCategoryId, selectedSubCategoryId);


        $('#save-form').click(function() {
            const url = "{{ route('clients.update', $client->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-form",
                data: $('#save-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
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
        })

        <x-forms.custom-field-filejs/>

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
