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
        <script type="text/javascript">window.CSRF_Token = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
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

                            @if(!empty($invoice))
                                <div class="cp-title cp-top-border cp-content">
                                    <span class="cp-key">Betrag</span>
                                    <span class="cp-value">{{ $invoice->getFormattedAmount() }} EUR</span>
                                </div>
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

        @yield('footer_scripts')

        <style type="text/css">
            .mdl-tabs__tab-bar  {
                justify-content: flex-start !important;
            }

            .mdl-tabs__tab {
                width: 100%;
            }

            .cp-key {
                font-size: 14px;
            }
            .cp-value {
                font-size: 14px;
                float: right;
            }
            body {
                font-family: Roboto;
                font-weight: 300;
                font-size: 16px;
            }
            .cp-spinner {
                width: 14px;
                height: 14px;
            }
            .cp-spinner .mdl-spinner__circle {
                border-width: 1px;
            }
            .cp-countdown {
                float: right;
            }
            .cp-content {
                padding: 8px;
            }
            .cp-top-border {
                border-top: 1px solid rgba(0,0,0,.1);
            }
            .cp-progress {
                width:100%;
                min-height:5px;
            }
            .cp-header-wrapper {
                width: 100%;

                box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 1px 5px 0 rgba(0,0,0,.12);
                transition-duration: .2s;
                transition-timing-function: cubic-bezier(.4,0,.2,1);
                transition-property: max-height,box-shadow;
            }
            .cp-subheader-wrapper {
                width: 100%;
                margin-bottom: 5px;
            }
            span.cp-seller-text {
                font-size: 22px;
            }
            .cp-container {
                padding: 0;
                max-width: 400px;
                min-height:300px;
                margin: 15px auto 0px auto;
            }
            .cp-seller {
                padding: 24px 8px;
            }
            .cp-time {
                font-weight: 300;
            }
            .cp-title {
                font-weight:500;
            }
            .cp-seller-logo {
                max-width: 80%;
            }

            .mdl-textfield {
                width: 500px;
            }

            input[type=number] {
                text-align:right;
            }
            .cp-btn-wrapper {
                text-align: center;
                padding-bottom: 20px;
            }
            .cp-btn-wrapper a {
                width:200px;
            }


            /** Textfields height **/
            .mdl-textfield {
                padding: 10px 0 !important;
            }

            .mdl-textfield__label {
                top: 12px !important;
            }

            .mdl-textfield__label:after {
                bottom: 10px;
            }

            .mdl-textfield--floating-label.is-focused .mdl-textfield__label, .mdl-textfield--floating-label.is-dirty .mdl-textfield__label, .mdl-textfield--floating-label.has-placeholder .mdl-textfield__label {
                top: 0px !important;
            }

        </style>
    </body>

    {!! HTML::script(asset('/js/iframeResizer.contentWindow.min.js')) !!}
</html>