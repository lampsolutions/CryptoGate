@extends('layouts.app2')

@section('content')


    <div class="mdl-card__supporting-text" style="text-align: center;">
        <h6 style="margin: 0 0 10px;">
            @lang('Bitte wählen Sie eine Zahlungsmethode aus')
        </h6>


        <div class="mdl-grid mdl-grid--no-spacing">
            @if($invoice->isCurrencyEnabled('BTC'))
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: 0;">
                    <a href="{{ route('payments.pay', ['uuid' => $invoice->uuid, 'currency' => 'BTC']) }}{{$forwardQueryParam}}" class="mdl-button mdl-js-button cp-currency-select" style="height: auto;">
                        <div class="mdl-grid mdl-grid--no-spacing" style="margin: 5px">
                            <div class="section__text mdl-cell mdl-cell--10-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
                                <div class="section__circle-container__circle" style="width: 60px; float: left; margin-top: 20px;">
                                    <img src="/svg/btc.svg" >
                                </div>
                                <h5 style="font-size: 1.3em;">Bitcoin (BTC)</h5>
                                <p>{{ $invoice->getExchange($invoice->amount, 'BTC', $invoice->currency)->amount }} BTC</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if($invoice->isCurrencyEnabled('LTC'))
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: 0;">
                    <a href="{{ route('payments.pay', ['uuid' => $invoice->uuid, 'currency' => 'LTC']) }}{{$forwardQueryParam}}" class="mdl-button mdl-js-button cp-currency-select" style="height: auto;">
                        <div class="mdl-grid mdl-grid--no-spacing" style="margin: 5px">
                            <div class="section__text mdl-cell mdl-cell--10-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
                                <div class="section__circle-container__circle" style="width: 60px; float: left; margin-top: 20px;">
                                    <img src="/svg/ltc.svg" >
                                </div>
                                <h5 style="font-size: 1.3em;">Litecoin (LTC)</h5>
                                <p>{{ $invoice->getExchange($invoice->amount, 'LTC', $invoice->currency)->amount }} LTC</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if($invoice->isCurrencyEnabled('BCH'))
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: 0;">
                    <a href="{{ route('payments.pay', ['uuid' => $invoice->uuid, 'currency' => 'BCH']) }}{{$forwardQueryParam}}" class="mdl-button mdl-js-button cp-currency-select" style="height: auto;">
                        <div class="mdl-grid mdl-grid--no-spacing" style="margin: 5px">
                            <div class="section__text mdl-cell mdl-cell--10-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
                                <div class="section__circle-container__circle" style="width: 60px; float: left; margin-top: 20px;">
                                    <img src="/svg/bch.svg" >
                                </div>
                                <h5 style="font-size: 1.3em;">Bitcoin Cash (BCH)</h5>
                                <p>{{ $invoice->getExchange($invoice->amount, 'BCH', $invoice->currency)->amount }} BCH</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif


            @if($invoice->isCurrencyEnabled('DASH'))
                <div class="mdl-card mdl-shadow--2dp" style="width: 100%; min-height: 0;">
                    <a href="{{ route('payments.pay', ['uuid' => $invoice->uuid, 'currency' => 'DASH']) }}{{$forwardQueryParam}}" class="mdl-button mdl-js-button cp-currency-select" style="height: auto;">
                        <div class="mdl-grid mdl-grid--no-spacing" style="margin: 5px">
                            <div class="section__text mdl-cell mdl-cell--10-col-desktop mdl-cell--6-col-tablet mdl-cell--3-col-phone">
                                <div class="section__circle-container__circle" style="width: 60px; float: left; margin-top: 20px;">
                                    <img src="/svg/dash-n.svg" >
                                </div>
                                <h5 style="font-size: 1.3em;">Dash (DASH)</h5>
                                <p>{{ $invoice->getExchange($invoice->amount, 'DASH', $invoice->currency)->amount }} DASH</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

        </div>

    </div>


@endsection
