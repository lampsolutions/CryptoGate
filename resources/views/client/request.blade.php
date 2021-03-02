@extends('layouts.app2')

@section('content')

    <div class="cp-content">

        <form method="post" action="{{ route('invoicerequest.create', ['uuid'=>$invoiceRequest->uuid]) }}" class="form-horizontal">
            @csrf

            @if($formal=="du")
                <h6 style="margin: 0 0 10px;">Deine Kontaktdaten</h6>
            @else
                <h6 style="margin: 0 0 10px;">Ihre Kontaktdaten</h6>
            @endif

            <div style="float: left;" class="donate-input-45 mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" required="required" name="Vorname" id="Vorname">
                <label class="mdl-textfield__label" for="Vorname">Vorname</label>
            </div>

            <div style="float: right;" class="donate-input-45 mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" required="required" name="Nachname" id="Nachname">
                <label class="mdl-textfield__label" for="Nachname">Nachname</label>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" name="Firmenname" id="Firmenname">
                <label class="mdl-textfield__label" for="Firmenname">Firmenname (optional)</label>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" required="required" type="text" name="Straße" id="Straße">
                <label class="mdl-textfield__label" for="Straße">Straße</label>
            </div>

            <div style="float: left;" class="donate-input-45 mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" required="required" type="text" name="Postleitzahl" id="Postleitzahl">
                <label class="mdl-textfield__label" for="Postleitzahl">Postleitzahl</label>
            </div>

            <div style="float: right;" class="donate-input-45 mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" required="required" type="text" name="Ort" id="Ort">
                <label class="mdl-textfield__label" for="Ort">Ort</label>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <select class="mdl-textfield__input"  name="Land" id="Land">
                    <option value="Afghanistan">Afghanistan</option>
                    <option value="Ägypten">Ägypten</option>
                    <option value="Aland">Aland</option>
                    <option value="Albanien">Albanien</option>
                    <option value="Algerien">Algerien</option>
                    <option value="Amerikanisch-Samoa">Amerikanisch-Samoa</option>
                    <option value="Amerikanische Jungferninseln">Amerikanische Jungferninseln</option>
                    <option value="Andorra">Andorra</option>
                    <option value="Angola">Angola</option>
                    <option value="Anguilla">Anguilla</option>
                    <option value="Antarktis">Antarktis</option>
                    <option value="Antigua und Barbuda">Antigua und Barbuda</option>
                    <option value="Äquatorialguinea">Äquatorialguinea</option>
                    <option value="Argentinien">Argentinien</option>
                    <option value="Armenien">Armenien</option>
                    <option value="Aruba">Aruba</option>
                    <option value="Ascension">Ascension</option>
                    <option value="Aserbaidschan">Aserbaidschan</option>
                    <option value="Äthiopien">Äthiopien</option>
                    <option value="Australien">Australien</option>
                    <option value="Bahamas">Bahamas</option>
                    <option value="Bahrain">Bahrain</option>
                    <option value="Bangladesch">Bangladesch</option>
                    <option value="Barbados">Barbados</option>
                    <option value="Belgien">Belgien</option>
                    <option value="Belize">Belize</option>
                    <option value="Benin">Benin</option>
                    <option value="Bermuda">Bermuda</option>
                    <option value="Bhutan">Bhutan</option>
                    <option value="Bolivien">Bolivien</option>
                    <option value="Bosnien und Herzegowina">Bosnien und Herzegowina</option>
                    <option value="Botswana">Botswana</option>
                    <option value="Bouvetinsel">Bouvetinsel</option>
                    <option value="Brasilien">Brasilien</option>
                    <option value="Brunei">Brunei</option>
                    <option value="Bulgarien">Bulgarien</option>
                    <option value="Burkina Faso">Burkina Faso</option>
                    <option value="Burundi">Burundi</option>
                    <option value="Chile">Chile</option>
                    <option value="China">China</option>
                    <option value="Cookinseln">Cookinseln</option>
                    <option value="Costa Rica">Costa Rica</option>
                    <option value="Cote d'Ivoire">Cote d'Ivoire</option>
                    <option value="Dänemark">Dänemark</option>
                    <option selected="selected" value="Deutschland">Deutschland</option>
                    <option value="Diego Garcia">Diego Garcia</option>
                    <option value="Dominica">Dominica</option>
                    <option value="Dominikanische Republik">Dominikanische Republik</option>
                    <option value="Dschibuti">Dschibuti</option>
                    <option value="Ecuador">Ecuador</option>
                    <option value="El Salvador">El Salvador</option>
                    <option value="Eritrea">Eritrea</option>
                    <option value="Estland">Estland</option>
                    <option value="Europäische Union">Europäische Union</option>
                    <option value="Falklandinseln">Falklandinseln</option>
                    <option value="Färöer">Färöer</option>
                    <option value="Fidschi">Fidschi</option>
                    <option value="Finnland">Finnland</option>
                    <option value="Frankreich">Frankreich</option>
                    <option value="Französisch-Guayana">Französisch-Guayana</option>
                    <option value="Französisch-Polynesien">Französisch-Polynesien</option>
                    <option value="Gabun">Gabun</option>
                    <option value="Gambia">Gambia</option>
                    <option value="Georgien">Georgien</option>
                    <option value="Ghana">Ghana</option>
                    <option value="Gibraltar">Gibraltar</option>
                    <option value="Grenada">Grenada</option>
                    <option value="Griechenland">Griechenland</option>
                    <option value="Grönland">Grönland</option>
                    <option value="Großbritannien">Großbritannien</option>
                    <option value="Guadeloupe">Guadeloupe</option>
                    <option value="Guam">Guam</option>
                    <option value="Guatemala">Guatemala</option>
                    <option value="Guernsey">Guernsey</option>
                    <option value="Guinea">Guinea</option>
                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                    <option value="Guyana">Guyana</option>
                    <option value="Haiti">Haiti</option>
                    <option value="Heard und McDonaldinseln">Heard und McDonaldinseln</option>
                    <option value="Honduras">Honduras</option>
                    <option value="Hongkong">Hongkong</option>
                    <option value="Indien">Indien</option>
                    <option value="Indonesien">Indonesien</option>
                    <option value="Irak">Irak</option>
                    <option value="Iran">Iran</option>
                    <option value="Irland">Irland</option>
                    <option value="Island">Island</option>
                    <option value="Israel">Israel</option>
                    <option value="Italien">Italien</option>
                    <option value="Jamaika">Jamaika</option>
                    <option value="Japan">Japan</option>
                    <option value="Jemen">Jemen</option>
                    <option value="Jersey">Jersey</option>
                    <option value="Jordanien">Jordanien</option>
                    <option value="Kaimaninseln">Kaimaninseln</option>
                    <option value="Kambodscha">Kambodscha</option>
                    <option value="Kamerun">Kamerun</option>
                    <option value="Kanada">Kanada</option>
                    <option value="Kanarische Inseln">Kanarische Inseln</option>
                    <option value="Kap Verde">Kap Verde</option>
                    <option value="Kasachstan">Kasachstan</option>
                    <option value="Katar">Katar</option>
                    <option value="Kenia">Kenia</option>
                    <option value="Kirgisistan">Kirgisistan</option>
                    <option value="Kiribati">Kiribati</option>
                    <option value="Kokosinseln">Kokosinseln</option>
                    <option value="Kolumbien">Kolumbien</option>
                    <option value="Komoren">Komoren</option>
                    <option value="Kongo">Kongo</option>
                    <option value="Kroatien">Kroatien</option>
                    <option value="Kuba">Kuba</option>
                    <option value="Kuwait">Kuwait</option>
                    <option value="Laos">Laos</option>
                    <option value="Lesotho">Lesotho</option>
                    <option value="Lettland">Lettland</option>
                    <option value="Libanon">Libanon</option>
                    <option value="Liberia">Liberia</option>
                    <option value="Libyen">Libyen</option>
                    <option value="Liechtenstein">Liechtenstein</option>
                    <option value="Litauen">Litauen</option>
                    <option value="Luxemburg">Luxemburg</option>
                    <option value="Macao">Macao</option>
                    <option value="Madagaskar">Madagaskar</option>
                    <option value="Malawi">Malawi</option>
                    <option value="Malaysia">Malaysia</option>
                    <option value="Malediven">Malediven</option>
                    <option value="Mali">Mali</option>
                    <option value="Malta">Malta</option>
                    <option value="Marokko">Marokko</option>
                    <option value="Marshallinseln">Marshallinseln</option>
                    <option value="Martinique">Martinique</option>
                    <option value="Mauretanien">Mauretanien</option>
                    <option value="Mauritius">Mauritius</option>
                    <option value="Mayotte">Mayotte</option>
                    <option value="Mazedonien">Mazedonien</option>
                    <option value="Mexiko">Mexiko</option>
                    <option value="Mikronesien">Mikronesien</option>
                    <option value="Moldawien">Moldawien</option>
                    <option value="Monaco">Monaco</option>
                    <option value="Mongolei">Mongolei</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Mosambik">Mosambik</option>
                    <option value="Myanmar">Myanmar</option>
                    <option value="Namibia">Namibia</option>
                    <option value="Nauru">Nauru</option>
                    <option value="Nepal">Nepal</option>
                    <option value="Neukaledonien">Neukaledonien</option>
                    <option value="Neuseeland">Neuseeland</option>
                    <option value="Neutrale Zone">Neutrale Zone</option>
                    <option value="Nicaragua">Nicaragua</option>
                    <option value="Niederlande">Niederlande</option>
                    <option value="Niederländische Antillen">Niederländische Antillen</option>
                    <option value="Niger">Niger</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Niue">Niue</option>
                    <option value="Nordkorea">Nordkorea</option>
                    <option value="Nördliche Marianen">Nördliche Marianen</option>
                    <option value="Norfolkinsel">Norfolkinsel</option>
                    <option value="Norwegen">Norwegen</option>
                    <option value="Oman">Oman</option>
                    <option value="Österreich">Österreich</option>
                    <option value="Pakistan">Pakistan</option>
                    <option value="Palästina">Palästina</option>
                    <option value="Palau">Palau</option>
                    <option value="Panama">Panama</option>
                    <option value="Papua-Neuguinea">Papua-Neuguinea</option>
                    <option value="Paraguay">Paraguay</option>
                    <option value="Peru">Peru</option>
                    <option value="Philippinen">Philippinen</option>
                    <option value="Pitcairninseln">Pitcairninseln</option>
                    <option value="Polen">Polen</option>
                    <option value="Portugal">Portugal</option>
                    <option value="Puerto Rico">Puerto Rico</option>
                    <option value="Réunion">Réunion</option>
                    <option value="Ruanda">Ruanda</option>
                    <option value="Rumänien">Rumänien</option>
                    <option value="Russische Föderation">Russische Föderation</option>
                    <option value="Salomonen">Salomonen</option>
                    <option value="Sambia">Sambia</option>
                    <option value="Samoa">Samoa</option>
                    <option value="San Marino">San Marino</option>
                    <option value="São Tomé und Príncipe">São Tomé und Príncipe</option>
                    <option value="Saudi-Arabien">Saudi-Arabien</option>
                    <option value="Schweden">Schweden</option>
                    <option value="Schweiz">Schweiz</option>
                    <option value="Senegal">Senegal</option>
                    <option value="Serbien und Montenegro">Serbien und Montenegro</option>
                    <option value="Seychellen">Seychellen</option>
                    <option value="Sierra Leone">Sierra Leone</option>
                    <option value="Simbabwe">Simbabwe</option>
                    <option value="Singapur">Singapur</option>
                    <option value="Slowakei">Slowakei</option>
                    <option value="Slowenien">Slowenien</option>
                    <option value="Somalia">Somalia</option>
                    <option value="Spanien">Spanien</option>
                    <option value="Sri Lanka">Sri Lanka</option>
                    <option value="St. Helena">St. Helena</option>
                    <option value="St. Kitts und Nevis">St. Kitts und Nevis</option>
                    <option value="St. Lucia">St. Lucia</option>
                    <option value="St. Pierre und Miquelon">St. Pierre und Miquelon</option>
                    <option value="St. Vincent/Grenadinen (GB)">St. Vincent/Grenadinen (GB)</option>
                    <option value="Südafrika, Republik">Südafrika, Republik</option>
                    <option value="Sudan">Sudan</option>
                    <option value="Südkorea">Südkorea</option>
                    <option value="Suriname">Suriname</option>
                    <option value="Svalbard und Jan Mayen">Svalbard und Jan Mayen</option>
                    <option value="Swasiland">Swasiland</option>
                    <option value="Syrien">Syrien</option>
                    <option value="Tadschikistan">Tadschikistan</option>
                    <option value="Taiwan">Taiwan</option>
                    <option value="Tansania">Tansania</option>
                    <option value="Thailand">Thailand</option>
                    <option value="Timor-Leste">Timor-Leste</option>
                    <option value="Togo">Togo</option>
                    <option value="Tokelau">Tokelau</option>
                    <option value="Tonga">Tonga</option>
                    <option value="Trinidad und Tobago">Trinidad und Tobago</option>
                    <option value="Tristan da Cunha">Tristan da Cunha</option>
                    <option value="Tschad">Tschad</option>
                    <option value="Tschechische Republik">Tschechische Republik</option>
                    <option value="Tunesien">Tunesien</option>
                    <option value="Türkei">Türkei</option>
                    <option value="Turkmenistan">Turkmenistan</option>
                    <option value="Turks- und Caicosinseln">Turks- und Caicosinseln</option>
                    <option value="Tuvalu">Tuvalu</option>
                    <option value="Uganda">Uganda</option>
                    <option value="Ukraine">Ukraine</option>
                    <option value="Ungarn">Ungarn</option>
                    <option value="Uruguay">Uruguay</option>
                    <option value="Usbekistan">Usbekistan</option>
                    <option value="Vanuatu">Vanuatu</option>
                    <option value="Vatikanstadt">Vatikanstadt</option>
                    <option value="Venezuela">Venezuela</option>
                    <option value="Vereinigte Arabische Emirate">Vereinigte Arabische Emirate</option>
                    <option value="Vereinigte Staaten von Amerika">Vereinigte Staaten von Amerika</option>
                    <option value="Vietnam">Vietnam</option>
                    <option value="Wallis und Futuna">Wallis und Futuna</option>
                    <option value="Weihnachtsinsel">Weihnachtsinsel</option>
                    <option value="Weißrussland">Weißrussland</option>
                    <option value="Westsahara">Westsahara</option>
                    <option value="Zentralafrikanische Republik">Zentralafrikanische Republik</option>
                    <option value="Zypern">Zypern</option>
                    <label class="mdl-textfield__label" for="Land">Land</label>
                </select>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" name="Email" id="Email">
                <label class="mdl-textfield__label" for="Email">Email (optional)</label>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" name="Telefon" id="Telefon">
                <label class="mdl-textfield__label" for="Telefon">Telefon (optional)</label>
            </div>

            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                <input class="mdl-textfield__input" type="text" name="Nachricht" id="Nachricht">
                <label class="mdl-textfield__label" for="Nachricht">Hinterlasse eine Nachricht an uns (optional)</label>
            </div>

            <hr/>

            <div class="btn-wrapper" style="    text-align: center; padding-bottom: 20px;">
                <button type="submit"
                        value="Individuell"
                        name="Individuell"
                        class="donate-sum-go-btn mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                    Weiter
                </button>
            </div>
        </form>

        <style type="text/css">
            .donate-input-45 {
                width: 48% !important;
            }

            .donate-sum-go-btn {
                width: 200px !important;
            }
        </style>
    </div>
@endsection