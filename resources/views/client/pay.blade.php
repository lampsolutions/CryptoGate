@extends('layouts.app2')

@section('cp-header')
    <div class="cp-time cp-top-border cp-content">
        <div class="cp-spinner mdl-spinner mdl-js-spinner is-active"></div>
        <span>Warte auf Zahlungseingang</span>
        <span class="cp-countdown payment-countdown-timer"></span>
        <div id="expiration-progress-bar" class="cp-progress mdl-progress mdl-js-progress"></div>
    </div>
@endsection

@section('content')

    <div class="cp-tabs mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
        <!-- Tab Bars -->
        <div class="mdl-tabs__tab-bar">
            <a href="#qr-normal-panel" class="mdl-tabs__tab is-active">QR-Normal</a>
            <a href="#qr-legacy-panel" class="mdl-tabs__tab">QR-Legacy</a>
            <a href="#copy-panel" class="mdl-tabs__tab">Details</a>
        </div>

        <div class="mdl-tabs__panel is-active" id="qr-normal-panel">
            <img style="display:block; margin: 0 auto; padding: 15px; max-width: 100%;" src="{{ $invoicePayment->getQRCodeDataUri() }}" />
            <div class="cp-btn-wrapper">
            <a href="{{ $invoicePayment->electrum_uri }}" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                In Wallet Öffnen
            </a>
            </div>
        </div>

        <div class="mdl-tabs__panel" id="qr-legacy-panel">
            <img style="display:block; margin: 0 auto; padding: 15px; max-width: 100%;" src="{{ $invoicePayment->getQRCodeDataUri($invoicePayment->getLegacyUri()) }}" />
            <div class="cp-btn-wrapper">
            <a href="{{ $invoicePayment->getLegacyUri() }}" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                In Wallet Öffnen
            </a>
            </div>
        </div>

        <div class="mdl-tabs__panel" id="copy-panel">
            <div class="cp-subheader-wrapper">

                <div class="cp-title cp-top-border cp-content" style="line-height: 30px;">
                    <span class="cp-key">Betrag </span>
                    <span class="cp-value">
                        {{ $invoicePayment->getFullCurrencyAmount() }} {{ $invoicePayment->currency }}
                        <button
                                id="amount-copy"
                                data-clipboard-text="{{ $invoicePayment->getFullCurrencyAmount() }}"
                                class="btn-clipboard mdl-button mdl-js-button mdl-button--icon"
                                style="background: transparent !important; color: rgba(0,0,0,.54) !important;">
                                    <i class="material-icons">attach_file</i>
                        </button>
                        <div data-copied-text="Kopiert!" data-default-text="Betrag in Zwischenablage kopieren" class="mdl-tooltip mdl-tooltip--left" for="amount-copy">
                            Betrag in Zwischenablage kopieren
                        </div>
                    </span>
                </div>

                <div class="cp-title cp-top-border cp-content" style="line-height: 30px;">
                    <span class="cp-key">Adresse</span>
                    <span class="cp-value" style="font-size: 12px; line-height: 20px;">
                        {{ $invoicePayment->electrum_address }}
                        <button
                                id="addr-copy"
                                data-clipboard-text="{{ $invoicePayment->electrum_address }}"
                                class="btn-clipboard mdl-button mdl-js-button mdl-button--icon"
                                style="background: transparent !important; color: rgba(0,0,0,.54) !important;">
                        <i class="material-icons">attach_file</i>
                    </button>
                    <div data-copied-text="Kopiert!" data-default-text="Adresse in Zwischenablage kopieren" class="mdl-tooltip mdl-tooltip--left" for="addr-copy">
                        Adresse in Zwischenablage kopieren
                    </div>
                    </span>
                </div>

                @if($invoicePayment->currency == 'BCH')
                    <div class="cp-title cp-top-border cp-content" style="line-height: 30px;">
                        <span class="cp-key">Legacy Adresse</span>
                        <span class="cp-value" style="font-size: 12px; line-height: 20px;">{{ \App\Lib\CashAddress::new2old($invoicePayment->electrum_address,true) }}</span>
                    </div>
                @endif
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
            $.ajax('{{ route('payments.check', ['uuid' => $invoice->uuid ])  }}').done(function(result) {
                if(result.paid) {
                    window.location.reload();
                }
            });
        }, 5000);


        var expiration_time = new Date(parseInt({{ $invoicePayment->getUnixExpirationTime() }} * 1000));
        var milliseconds_left = (new Date(expiration_time).getTime() - new Date().getTime());
        window.setTimeout(function() {

        }, milliseconds_left);

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