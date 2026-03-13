@php
    // Fonction pour ajouter le filigrane
    if (!function_exists('addWatermark')) {
        function addWatermark($text, $angle = -45) {
            // Dimensions de la feuille A4
            $a4Width = 596 ;  // Largeur en pixels
            $a4Height = 842; // Hauteur en pixels

            // Configuration du style du filigrane
            $style = 'font-family: helvetica, sans-serif; font-size: 180px; color: rgba(140, 180, 205, 0.5); position: absolute; transform: rotate(' . $angle . 'deg); pointer-events: none;';

        // Calcul de la position pour centrer le filigrane
        $x = ($a4Width - (140 * strlen($text) / 2)) / 2; // Centrer horizontalement
        $y = ($a4Height / 2)+30; // Centrer verticalement (approximativement)

            // Génération du filigrane
            return '<div style="' . $style . ' top: ' . $y . 'px; left: ' . $x . 'px;">' . htmlspecialchars($text) . '</div>';
        }
    }

    // Affichage du filigrane avec l'année en cours
    $watermarkText = date('Y');
    $watermark = addWatermark($watermarkText);
    $ct = $contract->subject;
@endphp

<style>
    .card-body-paper {
        position: relative; /* Nécessaire pour le positionnement du filigrane */
        width: 827px; /* Largeur A4 en pixels */
        height: 1170px; /* Hauteur A4 en pixels */
        padding: 20px;
        border: 1px solid #ccc;
        background-color: #fff;
    }

    .watermark {
        z-index: -1; /* Pour que le filigrane soit derrière le contenu */
    }

    .logo {
        height: 50px;
    }

    .signature_wrap {
        position: relative;
        height: 150px;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width: 400px;
    }

    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width: 400px;
        height: 150px;
    }

    .ml{
        margin-left : -20px;
        padding: 8px!important;
        border-radius: 4px;
        margin-top: -1px!important
    }
    .logo {
        height: 50px;
    }

    .signature_wrap {
        position: relative;
        height: 150px;
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width: 400px;
    }

    .signature-pad {
        position: absolute;
        left: 0;
        top: 0;
        width: 400px;
        height: 150px;
    }

</style>
<div class="row mb-4" style="background-color:gold;">
    <marquee behavior="" direction="">Votre enregistrement a été bien prise en compte, nous procédons à l'analyse des éléments soumis. Si les éléments soumis ok, une notification vous sera envoyé via votre mail et sur votre numéro WhatsApp. Veillez vous connectez pour suivre l'évolution <a href="https://dc-knowing.com/CRM/public/login" target="_blank">Lien de connexion</a>.</marquee>
</div>

<h4><b>{{$ct}}</b></h4>

<div class="card border-0 invoice">
    <!-- CARD BODY START -->
    <div class="card-body">
        <div class="invoice-table-wrapper" id="invoice-wrapper">
            @if($ct == 'Renouvellement d\'attestation d\'adhésion')
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-7">
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <p><br></p>
                            <p><br></p>
                            <table border="0" style="width:100%;">
                            <tbody>
                            <tr>
                            <td style="width:50%;">
                            <p style="text-align:center;">MINIST&#200;RE DES FINANCES ET DU BUDGET<br>---------------<br>DIRECTION G&#201;N&#201;RALE DES IMP&#212;TS&#160;</p>
                            </td>
                            <td style="width:50%;">
                            <p style="text-align:center;">REPUBLIQUE DE COTE D&#8217;IVOIRE<br>Union &#8211; Discipline &#8211; Travail<br>&#160;---------------</p>
                            </td>
                            </tr>
                            </tbody>
                            </table>
                            <p><span style="font-size:12pt;">&#160; &#160;</span></p>
                            <p><span style="font-size:12pt;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;</span></p>
                            <p><strong><u>REF</u></strong><strong>. N : {{$contract->client->clientDetails->numadh}}</strong></p>
                            <p><strong><u>&#160;</u></strong></p>
                            <p style="text-align:center;"><span style="font-size:14pt;"><strong><u>ATTESTATION D&#8217;ADHESION AU CENTRE DE GESTION AGREE</u></strong></span></p>
                            <p>Nous soussign&#233;s,<br>Centre de Gestion Agr&#233;&#233; D&#233;nomm&#233;&#160;: <strong>DC-KNOWING CGA SARL </strong><br>Compte Contribuable&#160;: <strong>1864699 A</strong><br>Forme Juridique&#160;: <strong>SARL</strong><br>Siege social : <strong>COCODY II Plateaux 7<sup>&#232;me</sup> tranche</strong><br>Ayant obtenu l&#8217;agr&#233;ment pour exercer en qualit&#233; de Centre de Gestion Agr&#233;e sous le num&#233;ro&#160;: <strong>296/SEPMBPE/DGI DU 29 MARS 2018</strong></p>
                            <p><strong>&#160;</strong></p>
                            <p>D&#233;clare que l&#8217;entit&#233; <strong>&#171; {{$contract->client->clientDetails->company_name}} &#187;</strong> est adh&#233;rente de notre &#233;tablissement depuis le <strong>{{ \Carbon\Carbon::parse($contract->client->clientDetails->skype)->format('d-m-Y') }}</strong>&#160;sous le num&#233;ro d&#8217;enregistrement&#160;:<strong> {{$contract->client->clientDetails->numadh}}</strong></p>
                            <li>&#8211; Nom commercial de l&#8217;adh&#233;rent&#160;<strong>: {{$contract->client->clientDetails->company_name}}</strong></li>
                            <li>&#8211; Localisation : <strong>{{$contract->client->clientDetails->address}}</strong></li>
                            <li>&#8211; NCC de l&#8217;adh&#233;rent&#160;<strong>: {{$contract->client->clientDetails->gst_number}}</strong></li>
                            <li>&#8211; Forme juridique :<strong> {{$contract->client->clientDetails->formjurid}}</strong></li>
                            <li>&#8211; R&#233;gime d&#8217;imposition : <strong>{{$contract->client->clientDetails->regime}}</strong></li>
                            <li>&#8211; CDI de rattachement : <strong>{{$contract->client->clientDetails->imp_centre}}</strong></li>
                            <li>&#8211; Tel : <strong>{{$contract->client->clientDetails->office}}</strong><strong></strong></li>
                            </ol>
                            <p><strong></strong></p>
                            <p><strong></strong></p>
                            <table border="0" style="height:104px;width:100.069%;">
                            <tbody>
                            <tr style="height:35px;">
                            <td style="width:33.3333%;height:35px;"></td>
                            <td style="width:33.3333%;height:35px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:35px;"><span style="font-size:10pt;"><strong>Fait &#224; Abidjan, le {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span></td>
                            </tr>
                            <tr style="height:27px;">
                            <td style="width:33.3333%;height:27px;"></td>
                            <td style="width:33.3333%;height:27px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:27px;">&#160;<u>Le G&#233;rant</u></td>
                            </tr>
                            <tr style="height:9px;">
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:9px;"></td>
                            </tr>
                            <tr style="height:9px;">
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:9px;"></td>
                            </tr>
                            <tr style="height:24px;">
                            <td style="width:33.3333%;height:24px;"></td>
                            <td style="width:33.3333%;height:24px;"></td>
                            <td style="width:33.3333%;vertical-align:bottom;height:24px;"><strong>KEYMAN Constant</strong></td>
                            </tr>
                            </tbody>
                            </table>
                        </div>
                        <p></p>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            @elseif($ct == 'Nouvelle adhésion')
                <div class="row" id="div_new" style="display:flex;justify-content: center; align-items: center;">
                    <div class="col-md-2"></div>
                    <div class="col-md-7">
                        <div class="card-body-paper">
                            {!! $watermark !!} <!-- Injection du filigrane -->
                            <p></p>
                            <p></p>
                            <p></p>
                            <div><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160</span></div>
                            <p></p>
                            <p style="text-align:left;"></p>
                            <table border="0" style="width:100%;">
                            <tbody>
                            <tr>
                            <td style="width:50%;">
                            <p style="text-align:center;">MINIST&#200;RE DES FINANCES ET DU BUDGET<br>---------------<br>DIRECTION G&#201;N&#201;RALE DES IMP&#212;TS&#160;</p>
                            </td>
                            <td style="width:50%;">
                            <p style="text-align:center;">REPUBLIQUE DE COTE D&#8217;IVOIRE<br>Union &#8211; Discipline &#8211; Travail<br>&#160;---------------</p>
                            </td>
                            </tr>
                            </tbody>
                            </table>
                            <div></div>
                            <p>&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;&#160;</p>
                            <p>&#160;</p>
                            <p>&#160;</p>
                            <p style="text-align:center;"><span style="font-size:24pt;"><strong>CABINET DC-KNOWING CGA</strong></span></p>
                            <p style="text-align:center;"><span style="font-size:18pt;"><strong>CENTRE DE GESTION AGREE</strong></span></p>
                            <table border="4" style="width:100%;border-style:solid;border-color:#000000;margin-left:auto;margin-right:auto;">
                            <tbody>
                            <tr>
                            <td style="width:100%;">
                            <p style="text-align:center;"><span style="font-family:'times new roman', times, serif;font-size:14pt;"><strong>CONTRAT D&#8217;ADHESION</strong></span></p>
                            <p style="text-align:center;"><span style="font-family:'times new roman', times, serif;font-size:14pt;"><strong></strong></span></p>
                            <p style="text-align:center;">Valant r&#232;glement int&#233;rieur au sens de l&#8217;article 62<sup>e</sup> du d&#233;cret</p>
                            <p style="text-align:center;">N&#176; 2002 &#8211; 146 du 11/03/2002 et aux sens des articles</p>
                            <p style="text-align:center;">2 et 3 de l&#8217;arr&#234;t&#233; N&#176;049/MEMEF/DGI du 04/04/2002</p>
                            </td>
                            </tr>
                            </tbody>
                            </table>
                            <p></p>
                            <p>&#160;</p>
                            <p></p>
                            <p style="text-align:right;">&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;</p>
                            <p style="text-align:center;"><span style="font-size:14pt;"><strong>{{$contract->client->clientDetails->numadh}}</strong></span></p>
                            <p style="text-align:center;"><span style="font-size:14pt;">&#160;</span></p>
                            <p style="text-align:center;"><span style="font-size:14pt;">&#160;</span></p>
                            <p style="text-align:center;"><em><span style="font-size:14pt;color:#999999;"><strong>{{$contract->client->clientDetails->company_name}}</strong></span></em></p>
                            <p></p>
                            <p></p>
                            <p></p>
                            <p></p>
                            <p></p>
                        </div>
                        <p></p>
                        <p pagebreak="true"></p>
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <p><br></p>
                            <p><br></p>
                            <table border="0" style="width:100%;">
                            <tbody>
                            <tr>
                            <td style="width:50%;">
                            <p style="text-align:center;">MINIST&#200;RE DES FINANCES ET DU BUDGET<br>---------------<br>DIRECTION G&#201;N&#201;RALE DES IMP&#212;TS&#160;</p>
                            </td>
                            <td style="width:50%;">
                            <p style="text-align:center;">REPUBLIQUE DE COTE D&#8217;IVOIRE<br>Union &#8211; Discipline &#8211; Travail<br>&#160;---------------</p>
                            </td>
                            </tr>
                            </tbody>
                            </table>
                            <p><span style="font-size:12pt;">&#160; &#160;</span></p>
                            <p><span style="font-size:12pt;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;</span></p>
                            <p><strong><u>REF</u></strong><strong>. N : {{$contract->client->clientDetails->numadh}}</strong></p>
                            <p><strong><u>&#160;</u></strong></p>
                            <p style="text-align:center;"><span style="font-size:14pt;"><strong><u>ATTESTATION D&#8217;ADHESION AU CENTRE DE GESTION AGREE</u></strong></span></p>
                            <p>Nous soussign&#233;s,<br>Centre de Gestion Agr&#233;&#233; D&#233;nomm&#233;&#160;: <strong>DC-KNOWING CGA SARL </strong><br>Compte Contribuable&#160;: <strong>1864699 A</strong><br>Forme Juridique&#160;: <strong>SARL</strong><br>Siege social : <strong>COCODY II Plateaux 7<sup>&#232;me</sup> tranche</strong><br>Ayant obtenu l&#8217;agr&#233;ment pour exercer en qualit&#233; de Centre de Gestion Agr&#233;e sous le num&#233;ro&#160;: <strong>296/SEPMBPE/DGI DU 29 MARS 2018</strong></p>
                            <p><strong>&#160;</strong></p>
                            <p>D&#233;clare que l&#8217;entit&#233; <strong>&#171; {{$contract->client->clientDetails->company_name}} &#187;</strong> est adh&#233;rente de notre &#233;tablissement depuis le <strong>{{ \Carbon\Carbon::parse($contract->client->clientDetails->skype)->format('d-m-Y') }}</strong>&#160;sous le num&#233;ro d&#8217;enregistrement&#160;:<strong> {{$contract->client->clientDetails->numadh}}</strong></p>
                            <ol>
                            <li>&#8211; Nom commercial de l&#8217;adh&#233;rent&#160;<strong>: {{$contract->client->clientDetails->company_name}}</strong></li>
                            <li>&#8211; Localisation : <strong>{{$contract->client->clientDetails->address}}</strong></li>
                            <li>&#8211; NCC de l&#8217;adh&#233;rent&#160;<strong>: {{$contract->client->clientDetails->gst_number}}</strong></li>
                            <li>&#8211; Forme juridique :<strong> {{$contract->client->clientDetails->formjurid}}</strong></li>
                            <li>&#8211; R&#233;gime d&#8217;imposition : <strong>{{$contract->client->clientDetails->regime}}</strong></li>
                            <li>&#8211; CDI de rattachement : <strong>{{$contract->client->clientDetails->imp_centre}}</strong></li>
                            <li>&#8211; Tel : <strong>{{$contract->client->clientDetails->office}}</strong><strong></strong></li>
                            </ol>
                            <p><strong></strong></p>
                            <p><strong></strong></p>
                            <table border="0" style="height:104px;width:100.069%;">
                            <tbody>
                            <tr style="height:35px;">
                            <td style="width:33.3333%;height:35px;"></td>
                            <td style="width:33.3333%;height:35px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:35px;"><span style="font-size:10pt;"><strong>Fait &#224; Abidjan, le {{\Carbon\Carbon::parse($contract->start_date)->format('d-m-Y')}}</strong></span></td>
                            </tr>
                            <tr style="height:27px;">
                            <td style="width:33.3333%;height:27px;"></td>
                            <td style="width:33.3333%;height:27px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:27px;">&#160;<u>Le G&#233;rant</u></td>
                            </tr>
                            <tr style="height:9px;">
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:9px;"></td>
                            </tr>
                            <tr style="height:9px;">
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;height:9px;"></td>
                            <td style="width:33.3333%;vertical-align:top;height:9px;"></td>
                            </tr>
                            <tr style="height:24px;">
                            <td style="width:33.3333%;height:24px;"></td>
                            <td style="width:33.3333%;height:24px;"></td>
                            <td style="width:33.3333%;vertical-align:bottom;height:24px;"><strong>KEYMAN Constant</strong></td>
                            </tr>
                            </tbody>
                            </table>
                        </div>
                        <p></p>
                        <p pagebreak="true"></p>
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <p><strong><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Entre les soussign&#233;s</span></strong><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">1.-Nom du Centre de Gestion Agr&#233;&#233; (CGA) : <strong>DC-KNOWING CGA </strong></span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">Forme juridique&#160;: <strong>SARL</strong></span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">Montant du capital social&#160;: <strong>1.000.000&#160; FCFA</strong></span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">Adresse du si&#232;ge social : <strong>ABIDJAN, Cocody, II Plateaux 7<sup>&#232;me</sup> tranche </strong></span></p>
                            <p style="text-align:left;"><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; -T&#233;l : <strong>(225) 59 76 72 88&#160; /(225) 72 92 30 68</strong> </span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; -E-Mail : <strong><u>dcknowing@gmail.com</u></strong></span></p>
                            <p style="text-align:left;"><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">N&#176; d'inscription au registre du commerce et du cr&#233;dit mobilier <strong>: CI-ABJ-2018-B-31734</strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">N&#176; de compte contribuable RCCM&#160;: <strong>1864699 A</strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Date et num&#233;ro de l'arr&#234;t&#233; d'agr&#233;ment&#160;: <strong>296/SEPMBPE/DGI DU 29 MARS 2018</strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Nom, pr&#233;nom et qualit&#233; du signataire : <strong>Monsieur KEYMAN Constant</strong>, <strong>G&#233;rant</strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Ci-apr&#232;s d&#233;sign&#233; le&#171; <strong>Centre</strong>&#187;</span></p>
                            <p><span style="font-size:8pt;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; d'une part,</span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong>Et</strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">2- Nom du client: <strong>{{$contract->client->clientDetails->company_name}}</strong></span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Forme Juridique : <strong>{{$contract->client->clientDetails->formjurid}}&#160;&#160;</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Sigle<strong> :</strong> <strong>{{$contract->client->clientDetails->state}}</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Montant du capital social<strong>: {{$contract->client->clientDetails->montcapit}} FCFA</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Adresse du si&#232;ge social :<strong> {{$contract->client->clientDetails->address}} - </strong>Section<strong> : {{$contract->client->clientDetails->section}} &#8211; </strong>Parcelle<strong> : {{$contract->client->clientDetails->parcelle}}&#160;</strong></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;- T&#233;l. :{{$contract->client->clientDetails->office}}<strong>&#160;-</strong> Fax : {{$contract->client->clientDetails->office}}</span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; - E-mail: {{$contract->client->clientDetails->electronic_address}}</span></p>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">3- Principales activit&#233;s&#160;<strong>: {{$contract->client->clientDetails->acti_prin}}</strong></span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">N&#176; d'inscription au registre du commerce et du cr&#233;dit mobilier : <strong>{{$contract->client->clientDetails->tax_name}}</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">N&#176; de compte contribuable: <strong>{{$contract->client->clientDetails->gst_number}}</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">R&#233;gime fiscal applicable : <strong>{{$contract->client->clientDetails->regime}}</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Code activit&#233; &#233;conomique : <strong>{{$contract->client->clientDetails->codeacti}}</strong></span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Nom, pr&#233;nom et qualit&#233; du signataire&#160;<strong>:{{--$contract->client->clientDetails->identification}}, {{$contract->client->clientDetails->fonction--}}</strong></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Ci-apr&#232;s d&#233;sign&#233; &#171;l&#8217;<strong>Adh&#233;rent</strong>&#187;,&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; d&#8217;autre part</span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Il a &#233;t&#233; convenu ce qui suit</span></p>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 1<sup>er</sup></u></strong><strong> : OBJET </strong></span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">Le pr&#233;sent contrat a pour objet de confier au Centre de Gestion Agr&#233;&#233;, DC-KNOWING, la mission d'assister l'Adh&#233;rent en mati&#232;re de gestion et de formation dans les domaines financier, comptable, juridique, commercial et fiscal et de d&#233;finir les obligations devant &#234;tre remplies par l'adh&#233;rent pour permettre au Centre d'accomplir pleinement sa mission. A ce titre, le pr&#233;sent contrat :</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">d&#233;finit les relations entre le Centre et l'Adh&#233;rent au plan de leurs obligations et droits r&#233;ciproques,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">pr&#233;cise les modalit&#233;s de fonctionnement de ces relations.</span><p pagebreak="true"></p></li>
                            </ul>
                        </div>
                        <p></p>
                        <p pagebreak="true"></p>
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 2</u></strong><strong> : CADRE LEGAL ET REGLEMENTAIRE </strong></span><br><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">Le pr&#233;sent contrat s'ex&#233;cute dans le respect des dispositions l&#233;gales et r&#233;glementaires en vigueur, en particulier :</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">les textes relatifs au SYSCOHADA et &#224; l'OHADA ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">les textes fiscaux en vigueur, notamment l'article 34 de l'annexe fiscale &#224; la loi des finances n&#176; 2001-338 instituant des avantages au profit des Centres de Gestion Agr&#233;&#233;s et de leurs adh&#233;rents ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">le d&#233;cret n&#176;2002-146 du 11 mars 2002 instituant les Centres de Gestion Agr&#233;&#233;s;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">l'arr&#234;t&#233; n&#176;49/MEMEF/DGI du 09 ao&#251;t 2002 fixant les modalit&#233;s de d&#233;p&#244;t et d'instruction des demandes d'agr&#233;ment des Centres de Gestion Agr&#233;&#233;s;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><span style="font-family:helvetica, arial, sans-serif;">l'arr&#234;t&#233; n&#176;535/MEMEF/DGI du 30 d&#233;cembre 2002 portant cahier des charges des Centres de Gestions Agr&#233;&#233;s.</span></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 3</u></strong><strong>: OBLIGATIONS DU CENTRE </strong></span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le Centre s'oblige &#224; apporter &#224; l'Adh&#233;rent une assistance en mati&#232;re de gestion et de formation dans le domaine financier, comptable, juridique, commercial et fiscal. II agit, &#224; ce titre, dans le cadre des dispositions l&#233;gales et r&#233;glementaires en vigueur mentionn&#233;es ci-dessus. II est en outre, tenu aux obligations sp&#233;cifiques ci-apr&#232;s</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le Centre est tenu de fournir gratuitement &#224; l'Adh&#233;rent une affiche lui permettant de justifier, &#224; l'&#233;gard de sa client&#232;le, de sa qualit&#233; d'adh&#233;rent au Centre. Cette affiche reproduit le texte &#224; porter &#224; la connaissance de la client&#232;le de l'Adh&#233;rent.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le Centre est tenu de fournir &#224; l'Adh&#233;rent, dans les quatre (4) mois suivant la cl&#244;ture de son exercice, un dossier de gestion relatif &#224; la situation &#233;conomique et financi&#232;re de son entreprise.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Ce dossier comprend :</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">une analyse comparative des comptes d'exploitation et du bilan (&#224; partir du 2<sup>&#232;me</sup> exercice suivant celui de l'adh&#233;sion) ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">un tableau financier des ressources et des emplois (TAFIRE) le cas &#233;ch&#233;ant ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">un tableau de d&#233;termination du fonds de roulement ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">les ratios les plus usuels;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">un commentaire sur l'activit&#233; de l'entreprise.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le Centre est tenu d'organiser au profit de l'Adh&#233;rent des actions de formation (circulaires, </span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">r&#233;unions&#8230;). Il se tient &#224; la disposition de l'Adh&#233;rent pour tous renseignements ou &#233;claircissements compl&#233;mentaires concernant son dossier de gestion et plus g&#233;n&#233;ralement&#160; pour lui apporter des conseils, notamment en mati&#232;re de gestion, qu'il pourrait solliciter.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le Centre est tenu d'&#233;tablir les d&#233;clarations fiscales de l'Adh&#233;rent destin&#233;es &#224; l'Administration Fiscale. Ces d&#233;clarations doivent &#234;tre vis&#233;es et rev&#234;tues du cachet du dirigeant du Centre.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L'ensemble des documents servant de base &#224; l'&#233;tablissement de ces d&#233;clarations doivent &#234;tre, &#233;galement, vis&#233;s par le dirigeant du Centre. En outre, ces d&#233;clarations ne peuvent porter que sur une p&#233;riode au cours de laquelle l'int&#233;ress&#233; &#233;tait adh&#233;rent au Centre.</span></p>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 4</u></strong><strong> : OBLIGATIONS DE L'ADHERENT </strong></span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L'Adh&#233;rent s'oblige &#224; communiquer au Centre, dans le d&#233;lai d'un mois toutes les modifications &#233;ventuelles le concernant, &#224; savoir :</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">modifications de l'activit&#233;,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">arr&#234;t de l'activit&#233;,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">modification du capital social,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">modification de la forme juridique,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">changement d'adresse,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">changement de r&#233;gime fiscal,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">changement de dirigeants,</span></li>
                            </ul>
                            <p><span style="font-size:8pt;font-family:helvetica, arial, sans-serif;">&#160;Ainsi que tous les renseignements utiles aux bonnes relations entre l'Adh&#233;rent et le Centre.</span></p>
                        </div>
                        <p></p>
                        <p pagebreak="true"></p>
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L'Adh&#233;rent s'oblige &#224; faire &#233;tablir par le Centre, pour son compte, ses d&#233;clarations fiscales destin&#233;es &#224; l'Administration Fiscale. Ces d&#233;clarations doivent &#234;tre dat&#233;es et sign&#233;es par l'Adh&#233;rent.</span></li>
                            <li><span style="font-size:8pt;"><span style="font-family:helvetica, arial, sans-serif;">L'Adh&#233;rent s'oblige &#224; communiquer au Centre ses documents comptables mensuels dans les dix (10) jours suivant la fin du mois concern&#233;. A d&#233;faut, la responsabilit&#233; du Centre sera d&#233;gag&#233;e quant &#224; la tenue de la comptabilit&#233; et &#224; la production des d&#233;clarations fiscales de l'Adh&#233;rent dans les d&#233;lais l&#233;gaux.</span></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L&#8217;Adh&#233;rent doit &#233;galement, avant la fin du 2<sup>&#232;me</sup> mois suivant la cl&#244;ture de son exercice, communiquer au Centre :</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Les documents n&#233;cessaires &#224; l'&#233;laboration de ses &#233;tats financiers ainsi que tous les documents annexes obligatoires,</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Tous les renseignements compl&#233;mentaires n&#233;cessaires &#224; l'&#233;laboration du dossier de gestion et &#224; la pr&#233;paration de ses d&#233;clarations fiscales par le Centre.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L'Adh&#233;rent impos&#233; selon le r&#233;gime d&#8217;imp&#244;t synth&#233;tique doit communiquer au Centre les documents et renseignements n&#233;cessaires &#224; la confection d'une situation comptable interm&#233;diaire au titre des six (6) premiers mois de l'exercice.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Cette situation est fournie au plus tard le 31 Ao&#251;t de chaque ann&#233;e.</span></p>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">[Pour les entreprises nouvellement cr&#233;&#233;es au moment de leur adh&#233;sion au Centre, la premi&#232;re situation comptable interm&#233;diaire devra &#234;tre &#233;tablie au titre des six premiers mois d'activit&#233; et &#234;tre transmise &#224; l'Adh&#233;rent &#224; l'expiration du huiti&#232;me mois au plus tard].<a href="#_ftn1">[1]</a>4</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L'Adh&#233;rent s'engage &#224; garantir la sinc&#233;rit&#233; de ses recettes, de l'ensemble des documents ainsi que des renseignements transmis au Centre.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Dans le cadre de sa mission de contr&#244;le de coh&#233;rence et de vraisemblance des documents fournis par l'Adh&#233;rent, le Centre peut &#234;tre amen&#233;, dans certains cas, &#224; demander des renseignements ou &#233;claircissement compl&#233;mentaires. L'Adh&#233;rent est tenu d'apporter les r&#233;ponses au Centre dans un d&#233;lai de quinze (15) jours &#224; compter de la r&#233;ception de la demande de renseignements.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">A d&#233;faut de r&#233;ponse ou lorsque celle-ci laisse subsister en dernier ressort des incoh&#233;rences ou invraisemblances importantes, une commission compos&#233;e de trois (3) membres (le dirigeant du Centre, un agent du Centre des Imp&#244;ts et un membre du Comit&#233; Technique vis&#233; &#224; l'article 7 du d&#233;cret pr&#233;cit&#233; du 11 mars 2002) se r&#233;unit, le repr&#233;sentant du Comit&#233; Technique &#233;tant membre de droit. La commission donne son avis sur l'opportunit&#233; d'engager ou non la proc&#233;dure de r&#233;siliation pr&#233;vue &#224; l'article 6 du pr&#233;sent contrat.</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">L&#8217;Adh&#233;rent s'engage &#224; payer ses cotisations dans le mois de leur date d'exigibilit&#233;.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Sauf le cas de r&#233;siliation pour faute du Centre, la r&#233;siliation du pr&#233;sent contrat en cours d&#8217;ann&#233;e est sans effet sur l'exigibilit&#233; de la cotisation. En outre, celle-ci nepeut faire l&#8217;objet d'un prorata.</span></p>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 5 </u></strong><strong>: ENTREE EN VIGUEUR - DUREE </strong></span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le pr&#233;sent contrat prend effet &#224; la date de signature par les parties. Un exemplaire du contrat est remis &#224; l'Adh&#233;rent au jour de sa signature.</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Le pr&#233;sent contrat est conclu pour une dur&#233;e ind&#233;termin&#233;e qui prend fin dans les conditions mentionn&#233;es &#224; l'article 6 ci-apr&#232;s.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>ARTICLE 6 </u></strong><strong>: RESILIATION </strong></span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>R&#233;siliation &#224; l'initiative de l'Adh&#233;rent</u></strong></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation peut intervenir &#224; l'initiative de l'Adh&#233;rent. Elle r&#233;sultera notamment de la cessation d'activit&#233;, de la vente du fonds de commerce ou de la d&#233;faillance du Centre dans l'accomplissement de sa mission.</span> <br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation &#224; l'initiative de l'Adh&#233;rent doit &#234;tre signifi&#233;e par &#233;crit au Centre, quel qu&#8217;en soit le motif.</span> <br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Lorsque l'Adh&#233;rent ne pr&#233;cise pas la date de prise d'effet de la r&#233;siliation du contrat, celle-ci est r&#233;put&#233;e valoir pour l'exercice en cours, &#224; la date de r&#233;ception de sa notification au Centre.</span> <br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation ne peut en aucun cas entra&#238;ner le remboursement de la cotisation.</span> <br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><u>_________________________</u></span><br><span style="font-size:8pt;"><sup>4</sup>Cette disposition ne concerne que les entreprises qui, au moment o&#249; elles contractent avec le Centre, viennent de d&#233;marrer leurs activit&#233;s.</span></p>
                        </div>
                        <p></p><p pagebreak="true"></p>
                        <div class="card-body-paper">
                            {!! $watermark !!}
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>R&#233;siliation du fait de la Perte d'agr&#233;ment</u></strong></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation du pr&#233;sent contrat intervient de mani&#232;re automatique au jour de la perte par le Centre de son agr&#233;ment.</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>R&#233;siliation &#224; l&#8217;initiative du Centre</u></strong></span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation du pr&#233;sent contrat peut &#234;tre prononc&#233;e &#224; l'initiative du Centre en cas de manquements graves ou r&#233;p&#233;t&#233;s aux engagements ou obligations contenus dans le pr&#233;sent contrat.</span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Elle doit &#234;tre obligatoirement prononc&#233;e lorsque l'Adh&#233;rent a fait l'objet d'un redressement fiscal bas&#233; sur des man&#339;uvres frauduleuses caract&#233;ris&#233;es impliquant sa mauvaise foi.</span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La proc&#233;dure de r&#233;siliation &#224; l'initiative du Centre est la suivante :</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">l'Adh&#233;rent est avis&#233; par lettre recommand&#233;e avec accus&#233; de r&#233;ception qu'une proc&#233;dure de r&#233;siliation est engag&#233;e &#224; son encontre.</span></li>
                            </ul>
                            <p><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Cette lettre doit &#233;noncer les diff&#233;rents motifs qui ont conduit le Centre &#224; engager ladite proc&#233;dure. Elle l'invite &#224; pr&#233;senter sa d&#233;fense devant la commission de r&#233;siliation vis&#233;e &#224; l'article 4-5 ci-dessus dans un d&#233;lai de 15 jours &#224; compter de la r&#233;ception de la notification et doit indiquer &#224; l'Adh&#233;rent qu'il lui est impossible de venir prendre connaissance des &#233;l&#233;ments contenus dans son dossier et qu'il peut se faire assister d'un conseil. Cette lettre indique la date et l'heure de la r&#233;union de la commission de r&#233;siliation.</span> <br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Cette date ne peut &#234;tre fix&#233;e &#224; moins de 30 jours au plus t&#244;t apr&#232;s l'envoi de la lettre </span><br><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">recommand&#233;e.</span></p>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">Apr&#232;s avoir pris connaissance des explications &#233;crites ou orales de l'Adh&#233;rent, la commission peut d&#233;cider, soit :</span>
                            <ul>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">de renoncer &#224; la r&#233;siliation du pr&#233;sent contrat ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">d'adresser un avertissement &#224; l'Adh&#233;rent ;</span></li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">de r&#233;silier le pr&#233;sent contrat.</span></li>
                            </ul>
                            </li>
                            <li><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">La r&#233;siliation est notifi&#233;e &#224; l'Adh&#233;rent par le Centre par lettre recommand&#233;e avec accus&#233; de r&#233;ception.</span></li>
                            </ul>
                            <h3><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;">&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; Fait &#224; Abidjan, le <strong>{{\Carbon\Carbon::parse($contract->start_date)->format('d-m-Y')}}</strong></span></h3>
                            <table border="0" style="height:64px;width:100%;">
                            <tbody>
                            <tr style="height:16px;">
                            <td style="width:25%;height:16px;"><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>Pour&#160; l'Adh&#233;rent&#160;</u></strong></span></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong><u>Pour&#160; DC-KNOWING CGA</u></strong></span></td>
                            </tr>
                            <tr style="height:16px;">
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong>Le G&#233;rant</strong></span></td>
                            </tr>
                            <tr style="height:16px;">
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            </tr>
                            <tr style="height:16px;">
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"></td>
                            <td style="width:25%;height:16px;"><span style="font-family:helvetica, arial, sans-serif;font-size:8pt;"><strong>KEYMAN Constant</strong></span></td>
                            </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            @endif
        </div>
        <br>
        <hr class="mt-1 mb-1"> 

        <div class="mt-3" style="display:none;">
            @if ($contract->signature)
                <div class="d-flex flex-column float-right">
                    <h6>@lang('modules.estimates.clientsignature')</h6>
                    <img src="{{$contract->signature->signature}}" style="width: 200px;">
                    <p>{{ $contract->signature->full_name }}<br>
                        @lang('app.place'): {{ $contract->signature->place }}<br>
                        @lang('app.date'): {{ $contract->signature->date->timezone($company->timezone) }}</p>
                </div>
            @endif

            @if ($contract->company_sign)
                <div class="d-flex flex-column">
                    <h6>@lang('modules.estimates.companysignature')</h6>
                    <img src="{{$contract->company_signature}}" style="width: 200px;">
                    <p>@lang('app.date'): {{ $contract->sign_date->timezone($company->timezone) }}</p>
                    @if($contract->signer)
                        <p style="margin-top: -16px;">@lang('app.signBy') : {{ $contract->signer ? $contract->signer->name : '--' }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div id="signature-mod" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display:none;">
            <div class="modal-dialog d-flex justify-content-center align-items-center modal-xl">
                <div class="modal-content">
                    @include('estimates.ajax.accept-estimate')
                </div>
            </div>
        </div>
    </div>
    <!-- CARD BODY END -->


<!-- INVOICE CARD END -->

{{-- Custom fields data --}}
@if (isset($fields) && count($fields) > 0)
    <div class="row mt-4">
        <!-- TASK STATUS START -->
        <div class="col-md-12">
            <x-cards.data>
                <h5 class="mb-3"> @lang('modules.projects.otherInfo')</h5>
                <x-forms.custom-field-show :fields="$fields" :model="$contract"></x-forms.custom-field-show>
            </x-cards.data>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
// Fonction pour exporter tous les tableaux en un seul PDF
    function ExportToPDFALL() {
        var totalPages = parseInt(document.getElementById('cpte_table').value); // Nombre total de pages
        var month = $("#mois").val();
        var year = $("#annee").val();

        // Créer un élément conteneur pour tous les tableaux
        var container = document.createElement('div');
        container.appendChild(document.createElement('br'));
        var pages = document.querySelectorAll('.page');

        // Boucle pour chaque tableau
        for (var i = 0; i < totalPages; i++) {

            pages[i].style.display = 'block'; // Afficher chaque page
            pages[i].style.fontSize = '5px'; // Appliquer la police d'écriture 7px

            container.appendChild(pages[i]); 
            
            // Ajouter l'attribut style avec la taille d'une page A4
            pages[i].style.width = "8.27in"; // Largeur d'une page A4 en pouces
            pages[i].style.height = "11.7in"; // Hauteur d'une page A4 en pouces
        }

        // Options pour la conversion en PDF
        var opt = {
            filename: 'LIVRE_PAIE_APP_GRH_DC-KONWING_' + month + '-' + year, // Nom du fichier PDF
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { scale: 3, dpi: 72, letterRendering: true },  
            jsPDF: { unit: 'in', format: 'A4', orientation: 'portrait', margin: '1em' }
        };

        // Convertir et sauvegarder en PDF
        html2pdf().set(opt).from(container).save();
    }

    function toggleDivs() {
            const divNew = document.getElementById('div_new');
            const divRenew = document.getElementById('div_renew');
            
            if (divRenew.style.display === 'none') {
                divRenew.style.display = 'flex'; // Afficher div_renew
                divNew.style.display = 'none'; // Cacher div_new
            } else {
                divRenew.style.display = 'none'; // Cacher div_renew
                divNew.style.display = 'flex'; // Afficher div_new
            }
        }

    function saveAsPDF2() {
        var element = document.getElementById('invoice-wrapper');

        var opt = {
            filename: 'CONTRAT_CGA.pdf', // Nom du fichier PDF
            html2canvas: {
                scale: 3, // Échelle de conversion HTML à Canvas
                letterRendering: true
            },
            jsPDF: {
                unit: 'in', // Unité de mesure
                format: 'A4', // Format du papier
                orientation: 'portrait',
                margin: 1 // Marge en pouces
            }
        };

        // Utiliser html2pdf pour exporter le div en PDF
        html2pdf().set(opt).from(element).save();
    }

    $('#company-signature').click(function () {
        const url = "{{ route('contracts.company_sig', $contract->id) }}"; 
        $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_LG, url);
    });
    var canvas = document.getElementById('signature-pad');

    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });

    document.getElementById('clear-signature').addEventListener('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
    });

    document.getElementById('undo-signature').addEventListener('click', function (e) {
        e.preventDefault();
        var data = signaturePad.toData();
        if (data) {
            data.pop(); // remove the last dot or line
            signaturePad.fromData(data);
        }
    });

    $('#toggle-pad-uploader').click(function () {
        var text = $('.signature').hasClass('d-none') ? '{{ __("modules.estimates.uploadSignature") }}' : '{{ __("app.sign") }}';

        $(this).html(text);

        $('.signature').toggleClass('d-none');
        $('.upload-image').toggleClass('d-none');
    });

    $('#save-signature').click(function () {
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var email = $('#email').val();
        var signature = signaturePad.toDataURL('image/png');
        var image = $('#image').val();

        // this parameter is used for type of signature used and will be used on validation and upload signature image
        var signature_type = !$('.signature').hasClass('d-none') ? 'signature' : 'upload';

        if (signaturePad.isEmpty() && !$('.signature').hasClass('d-none')) {
            Swal.fire({
                icon: 'error',
                text: "{{ __('messages.signatureRequired') }}",

                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            });
            return false;
        }

        $.easyAjax({
            url: "{{ route('contracts.sign', $contract->id) }}",
            container: '#acceptEstimate',
            type: "POST",
            blockUI: true,
            file: true,
            disableButton: true,
            buttonSelector: '#save-signature',
            data: {
                first_name: first_name,
                last_name: last_name,
                email: email,
                signature: signature,
                image: image,
                signature_type: signature_type,
                _token: '{{ csrf_token() }}'
            },
        })
    });

    $('body').on('click', '.delete-table-row', function () {
        var id = $(this).data('contract-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('contracts.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.href = "{{ route('contracts.index') }}"
                        }
                    }
                });
            }
        });
    });
    var canvas = document.getElementById('sign-pad');

    var signPad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });

    document.getElementById('clear-sign').addEventListener('click', function (e) {
        e.preventDefault();
        signPad.clear();
    });

    document.getElementById('undo-sign').addEventListener('click', function (e) {
        e.preventDefault();
        var data = signPad.toData();
        if (data) {
            data.pop(); // remove the last dot or line
            signPad.fromData(data);
        }
    });

    $('#toggle-pad-upload').click(function () {
        var text = $('.signature').hasClass('d-none') ? '{{ __("modules.estimates.uploadSignature") }}' : '{{ __("app.sign") }}';

        $(this).html(text);

        $('.signature').toggleClass('d-none');
        $('.upload-img').toggleClass('d-none');
    });

    $('#save-sign').click(function () {
        var signature = signPad.toDataURL('image/png');
        var image = $('#sign_image').val();

        // this parameter is used for type of signature used and will be used on validation and upload signature image
        var signature_type = !$('.signature').hasClass('d-none') ? 'signature' : 'upload';

        if (signPad.isEmpty() && !$('.signature').hasClass('d-none')) {
            Swal.fire({
                icon: 'error',
                text: '{{ __('messages.signatureRequired') }}',

                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            });
            return false;
        }
    });

</script>
