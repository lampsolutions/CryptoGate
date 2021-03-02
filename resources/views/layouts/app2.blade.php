<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', Lang::get('titles.app')) }}</title>
        <link rel="shortcut icon" href="/favicon.ico">
        {!! HTML::style(asset('css/mdl-themes/material.min.css'), ['type' => 'text/css', 'rel' => 'stylesheet']) !!}
        {!! HTML::style('/roboto.css', ['type' => 'text/css', 'rel' => 'stylesheet']) !!}
        {!! HTML::style(asset('/material.css'), ['type' => 'text/css', 'rel' => 'stylesheet']) !!}



        @yield('template_linked_fonts')
        @yield('template_linked_css')
        @yield('head')

        @include("layouts.partials.branding")

    </head>
    <body>
        <div id="app">
            <div class="mdl-layout mdl-js-layout">
                <main id="layout-content" class="mdl-layout__content mdl-color--grey-100">

                    <div class="mdl-color--white mdl-shadow--2dp cp-container">
                        <div class="cp-header-wrapper mdl-color--grey-200">
                            <div class="cp-seller cp-top-border cp-content">
                                @if(!empty($branding['branding_use_logo']) && $branding['branding_use_logo'] == "true")
                                    <img class="cp-seller-logo" src="{{$branding['branding_logo_uri']}}" />
                                @else
                                    <span class="cp-seller-text">{{$branding['branding_seller_name']}}</span>
                                @endif
                            </div>

                            <div class="cp-title cp-top-border cp-content cp-sub-title">
                                <span class="cp-title-text">{{@$title}}</span>
                            </div>

                            @if(isset($invoicePayment) and !empty($invoicePayment))

                                <div class="cp-title cp-top-border cp-content">
                                    <span class="cp-key">@lang('Betrag')</span>
                                    <span class="cp-value">{{ $invoicePayment->getFullCurrencyAmount() }} {{ $invoicePayment->currency }}</span>
                                </div>
                                @if(!empty($invoice->note))
                                    <div class="cp-title cp-top-border cp-content">
                                        <span class="cp-key">@lang('Hinweis')</span>
                                        <span class="cp-value">{{ $invoice->note }}</span>
                                    </div>
                                @endif
                            @elseif(!empty($invoice))
                                <div class="cp-title cp-top-border cp-content">
                                    <span class="cp-key">@lang('Betrag')</span>
                                    <span class="cp-value">{{ $invoice->getFormattedAmount() }} {{ $invoice->getFiatCurrency() }}</span>
                                </div>
                                @if(!empty($invoice->note))
                                    <div class="cp-title cp-top-border cp-content">
                                        <span class="cp-key">@lang('Hinweis')</span>
                                        <span class="cp-value">{{ $invoice->note }}</span>
                                    </div>
                                @endif
                            @endif


                            @yield('cp-header')
                        </div>


                        @yield('content')

                    </div>
                    @include("layouts.partials.footer")
                </main>
            </div>

        </div>

        <script>
            if ( self !== top ) {
                document.getElementById('layout-content').classList.remove("mdl-color--grey-100");
            }
        </script>

        {!! HTML::script(asset('/js/jquery.min.js')) !!}
        {!! HTML::script(asset('/js/material.min.js')) !!}
        {!! HTML::script(asset('/js/textfield.js')) !!}

        @yield('footer_scripts')

        {!! HTML::script(asset('/js/iframeResizer.contentWindow.min.js')) !!}
    </body>
</html>
