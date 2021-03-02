@extends('layouts.app2')

@section('cp-header')
    <div class="cp-time cp-top-border cp-content">
        <div class="cp-spinner mdl-spinner mdl-js-spinner is-active"></div>
        <span>@lang('Warte auf Zahlungseingang')</span>
        <span class="cp-countdown payment-countdown-timer"></span>
        <div id="expiration-progress-bar" class="cp-progress mdl-progress mdl-js-progress"></div>
    </div>
@endsection

@section('content')


    <div class="cp-tabs mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
        <div class="mdl-tabs__panel is-active" id="copy-panel">
            <div class="cp-subheader-wrapper">
                <div id="network-fees-text" class="cp-info-wrapper" style="padding: 10px;">
                    <div style="color: red; font-size: 22px; margin-top: 10px;">@lang('Teilzahlung erkannt')</div><br>
                    <small>@lang('Es wurde eine Teilzahlung erkannt.')<br>@lang('Bitte überweisen Sie den restlichen Betrag an die unten genannte Addresse.')</small>
                </div>

                <div id="qr-code" style="display:none;">
                    <img style="display:block; margin: 0 auto; padding: 15px 0; width: 250px;" src="{{ $invoicePayment->getQRCodeDataUri($URI) }}" />
                </div>

                <div class="cp-title cp-top-border cp-content" style="line-height: 30px;">
                    <span class="cp-key">@lang('Restbetrag') </span>
                    <span class="cp-value">
                        {{$pending}} {{ $invoicePayment->currency }}
                        <button
                                id="amount-copy"
                                data-clipboard-text="{{$pending}}"
                                class="btn-clipboard mdl-button mdl-js-button mdl-button--icon"
                                style="background: transparent !important; color: rgba(0,0,0,.54) !important;">
                                    <i class="material-icons">attach_file</i>
                        </button>
                        <div data-copied-text="Kopiert!" data-default-text="@lang('Betrag in Zwischenablage kopieren')" class="mdl-tooltip mdl-tooltip--left" for="amount-copy">
                            @lang('Betrag in Zwischenablage kopieren')
                        </div>
                    </span>
                </div>

                <div class="cp-title cp-top-border cp-content" style="line-height: 30px;">
                    <span class="cp-key">@lang('Adresse')</span>
                    <span class="cp-value" style="font-size: 10px; line-height: 20px;">
                        {{ $invoicePayment->electrum_address }}
                        <button
                                id="addr-copy"
                                data-clipboard-text="{{ $invoicePayment->electrum_address }}"
                                class="btn-clipboard mdl-button mdl-js-button mdl-button--icon"
                                style="background: transparent !important; color: rgba(0,0,0,.54) !important;">
                        <i class="material-icons">attach_file</i>
                    </button>
                    <div data-copied-text="Kopiert!" data-default-text="@lang('Adresse in Zwischenablage kopieren')" class="mdl-tooltip mdl-tooltip--left" for="addr-copy">
                        @lang('Adresse in Zwischenablage kopieren')
                    </div>
                    </span>
                </div>
            </div>

            <div class="cp-btn-wrapper">
                <a href="{{ $URI }}" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    @lang('Wallet Öffnen')
                </a>
                <a id="toggle-qr" href="{{ $URI }}" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                    @lang('QR-Code Öffnen')
                </a>
            </div>


        </div>

    </div>
@endsection

@section('footer_scripts')
    <script type="text/javascript" src="{{asset('/js/clipboard.min.js')}}"></script>
    <script type="text/javascript">
        var clipboard = new ClipboardJS('.btn-clipboard');

        clipboard.on('success', function(e) {
            var data = $('[for="'+e.trigger.id+'"]').data();

            $('[for="'+e.trigger.id+'"]').text(data.copiedText);

            window.setTimeout(function() {
                $('[for="'+e.trigger.id+'"]').text(data.defaultText);
            }, 2000);

        });
    </script>

    @if($invoice->status!='Paid')
    <script type="text/javascript">
        window.setInterval(function() {
            $.ajax('{{ route('payments.check', ['uuid' => $invoice->uuid, 'paymentId' => $invoicePayment->electrum_id ])  }}').done(function(result) {
                if(result.paid != "partial") {
                    window.location.reload();
                }
            });
        }, 5000);


        var expiration_time = new Date(parseInt({{ $invoicePayment->getUnixExpirationTime() }} * 1000));
        var milliseconds_left = (new Date(expiration_time).getTime() - new Date().getTime());
        window.setTimeout(function() {

        }, milliseconds_left);

        $('#toggle-qr').on('click', function() {
            $('#qr-code').slideToggle("slow");
            $('#network-fees-text').slideToggle("slow");
            return false;
        });

        window.setInterval(function() {
            milliseconds_left = milliseconds_left - 1000;
            if(milliseconds_left < 0) window.location.reload();

            var date = new Date(milliseconds_left);
            var hh = date.getUTCHours();
            var mm = date.getUTCMinutes();
            var ss = date.getSeconds();

            if (hh < 10) {hh = "0"+hh;}
            if (mm < 10) {mm = "0"+mm;}
            if (ss < 10) {ss = "0"+ss;}
            var t = mm+":"+ss;

            $('.payment-countdown-timer').html(t);

            var seconds_left = milliseconds_left / 1000;

            var seconds_default = 60*15;

            var percent = parseInt(( (seconds_default - seconds_left) / seconds_default) * 100);

            if(percent > 75) {
                jQuery('.progressbar.bar1').attr('style', 'background-color: #F44336 !important;');
            }

            jQuery('#expiration-progress-bar').get(0).MaterialProgress.setProgress( percent );
        },1000);


        document.querySelector('#expiration-progress-bar').addEventListener('mdl-componentupgraded', function() {
            if({{$invoicePayment->getExpirationProgressPercent()}} > 75) {
                jQuery('.progressbar.bar1').attr('style', 'background-color: #F44336 !important;');
            }
            this.MaterialProgress.setProgress({{$invoicePayment->getExpirationProgressPercent()}});
        });

    </script>
    @endif

@endsection
