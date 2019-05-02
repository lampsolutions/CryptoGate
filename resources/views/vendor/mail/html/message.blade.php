@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            @if($branding['branding_use_logo'] == 'true')
                <img style="max-height: 100px" src="{{ $branding['branding_logo_uri'] }}" />
            @else
                {{ $branding['branding_seller_name']  }}
            @endif
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <a href="{{route('legal.impressum')}}">Impressum</a> | <a href="{{route('legal.privacy')}}">Datenschutz</a>
            @if(!empty(Cache::get('agb')))
                | <a href="{{route('legal.agb')}}">AGB</a>
            @endif
            <br/><br/>
            © {{ date('Y') }} {{ $branding['branding_seller_name']  }}.<br/>
            @lang('Alle Rechte vorbehalten.')
        @endcomponent
    @endslot
@endcomponent
