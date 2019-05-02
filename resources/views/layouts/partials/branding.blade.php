<style type="text/css">
    .cp-header-wrapper {
        @if(empty($branding['branding_primary_color']))
            background-color: #eee !important;
        @else
            background-color: {{$branding['branding_primary_color']}} !important;
        @endif

        @if(empty($branding['branding_primary_text_color']))
            color: rgba(0,0,0,.87);
        @else
            color: {{$branding['branding_primary_text_color']}};
        @endif
    }
    @if(!empty($branding['branding_secondary_color']))
    .mdl-progress>.progressbar {
        background-color: {{$branding['branding_secondary_color']}} !important;
    }
    .mdl-progress>.bufferbar {
        background-image: linear-gradient(to right,rgba(255,255,255,.7),rgba(255,255,255,.7)),linear-gradient(to right,{{$branding['branding_secondary_color']}} ,{{$branding['branding_secondary_color']}});
        z-index: 0;
        left: 0;
    }
    .mdl-tabs.is-upgraded .mdl-tabs__tab.is-active:after {
        background: {{$branding['branding_secondary_color']}} !important;
    }
    @endif

    @if(!empty($branding['branding_logo_align']) && $branding['branding_use_logo'] == 'true')
        div.cp-seller {
            text-align: {{$branding['branding_logo_align']}};
        }
    @else
        div.cp-seller {
        text-align: {{$branding['branding_primary_text_align']}};
        }
    @endif

    @if(!empty($branding['branding_primary_text_align']))
        div.cp-sub-title {
        text-align: {{$branding['branding_primary_text_align']}};
        }
    @endif

    .mdl-button {
        color: {{$branding['branding_secondary_text_color']}} !important;
        background: {{$branding['branding_secondary_color']}} !important;
    }

    .mdl-slider.is-upgraded {
        color: {{$branding['branding_secondary_color']}} !important;
    }

    .mdl-slider__background-lower {
        background: {{$branding['branding_secondary_color']}} !important;
    }

    .mdl-textfield--floating-label.is-focused .mdl-textfield__label, .mdl-textfield--floating-label.is-dirty .mdl-textfield__label, .mdl-textfield--floating-label.has-placeholder .mdl-textfield__label {
        color: {{$branding['branding_secondary_color']}} !important;
    }

    .mdl-textfield__label:after {
        background: {{$branding['branding_secondary_color']}} !important;
    }

    .mdl-button.cp-currency-select {
        background: white !important;
        color: black !important;
    }

</style>