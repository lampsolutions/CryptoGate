@extends('layouts.app2')

@section('content')

    <div class="cp-content">

        <br/>
        @if($formal=="du")
            <h6 style="margin: 0 0 30px; text-align: center;">Bitte wähle den gewünschten Betrag aus</h6>
        @else
            <h6 style="margin: 0 0 30px; text-align: center;">Bitte wählen Sie den gewünschten Betrag aus</h6>
        @endif


        <form method="post" action="{{ route($route.':create') }}" class="form-horizontal">
            @csrf


            <div class="slider-input-wrapper">

                <input style="width: 78%;" class="mdl-slider mdl-js-slider" type="range" name="Betrag-slider" id="Betrag-slider"
                       min="0" max="1000" step="1" value="{{ $prefilled }}" tabindex="0">

                <div class="slider-input mdl-textfield mdl-js-textfield mdl-textfield--floating-label" style="margin-bottom: 20px;">
                    <style type="text/css">
                        .mdl-slider__container {
                            width: 70%;
                            display: inline-flex;
                        }
                        .slider-input {
                            width: 20% !important;
                            display: inline-flex !important;
                        }
                        .btn-wrapper {
                            text-align: center;
                            padding-bottom: 20px;
                        }
                    </style>
                    <input style="width: 65px;" class="mdl-textfield__input" type="number" id="Betrag" name="Betrag" value="{{ $prefilled }}" min="0.01" max="10000.00" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" />
                    <div style="position: absolute; right: -25px; top: 13px; font-weight: bold;" class="mdl-button--file">
                        {{ $global_fiat_currency }}
                    </div>
                </div>
            </div>

            <div style="margin: 0 auto!important; padding-left: 20px; padding-right: 20px;">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" >
                    <input class="mdl-textfield__input" type="text" name="Nachricht" id="Nachricht">
                    <label class="mdl-textfield__label" for="Nachricht">Nachricht an uns (optional)</label>
                </div>
            </div>

            <br/><br/>

            <div class="btn-wrapper">
            <button type="submit"
                    value="Individuell"
                    name="Individuell"
                    class="donate-sum-go-btn mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                Weiter
            </button>
            </div>
            <input type="hidden" name="returnUrl" value="{{ $returnUrl }}">
            <input type="hidden" name="formal" value="{{ $formal }}">
            <input type="hidden" name="title" value="{{ $title }}">

        </form>


        <script>


            var slider = document.getElementById("Betrag-slider");
            var output = document.getElementById("Betrag");
            output.value = slider.value; // Display the default slider value

            // Update the current slider value (each time you drag the slider handle)
            slider.oninput = function() {
                output.value = this.value;
            };
            slider.onchange = function() {
                output.value = this.value;
            };

            output.oninput = function() {
                slider.value = this.value;
            };
        </script>

        <style type="text/css">
            .donate-sum-go-btn {
                width: 200px !important;
            }
        </style>

    </div>

@endsection