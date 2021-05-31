@extends('layouts.app2')


@section('content')

    <div class="mdl-card__supporting-text" style="text-align: center">
        <h4 style="color: black">Vielen Dank für Ihre Zahlung!</h4>

        <p class="cp-title-text" style="font-family: Roboto; font-weight: 500; font-size: 16px; line-height: 20px; color: black;">
            <?xml version="1.0" encoding="UTF-8" standalone="no"?>
            <svg
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                xmlns:cc="http://creativecommons.org/ns#"
                xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                xmlns:svg="http://www.w3.org/2000/svg"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                height="128"
                viewBox="0 0 128 128"
                width="128"
                version="1.1"
                id="svg6"
                sodipodi:docname="thumb_up_alt-24px.svg"
                inkscape:version="1.0.1 (3bc2e813f5, 2020-09-07)">
                <metadata
                    id="metadata12">
                    <rdf:RDF>
                        <cc:Work
                            rdf:about="">
                            <dc:format>image/svg+xml</dc:format>
                            <dc:type
                                rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
                            <dc:title></dc:title>
                        </cc:Work>
                    </rdf:RDF>
                </metadata>
                <defs
                    id="defs10" />
                <sodipodi:namedview
                    pagecolor="#ffffff"
                    bordercolor="#666666"
                    borderopacity="1"
                    objecttolerance="10"
                    gridtolerance="10"
                    guidetolerance="10"
                    inkscape:pageopacity="0"
                    inkscape:pageshadow="2"
                    inkscape:window-width="2560"
                    inkscape:window-height="1377"
                    id="namedview8"
                    showgrid="false"
                    inkscape:zoom="7.4246212"
                    inkscape:cx="60.577267"
                    inkscape:cy="55.86576"
                    inkscape:window-x="2552"
                    inkscape:window-y="-8"
                    inkscape:window-maximized="1"
                    inkscape:current-layer="svg6" />
                <path
                    d="M 128,128 H 0 V 0 h 128 z"
                    fill="none"
                    id="path2"
                    style="stroke-width:5.33333" />
                <path
                    d="m 10.666667,106.66667 h 10.666666 c 2.933334,0 5.333334,-2.4 5.333334,-5.33334 V 53.333333 C 26.666667,50.4 24.266667,48 21.333333,48 H 10.666667 Z M 116.42667,68.693333 C 117.01333,67.36 117.33333,65.92 117.33333,64.426667 v -5.76 C 117.33333,52.8 112.53333,48 106.66667,48 H 77.333333 L 82.24,23.2 C 82.506667,22.026667 82.346667,20.746667 81.813333,19.68 80.586667,17.28 79.04,15.093333 77.12,13.173333 L 74.666667,10.666667 40.48,44.853333 C 38.453333,46.88 37.333333,49.6 37.333333,52.426667 V 94.24 c 0,6.82667 5.6,12.42667 12.48,12.42667 h 43.253334 c 3.733333,0 7.253333,-1.97334 9.173333,-5.17334 z"
                    id="path4"
                    style="stroke-width:5.33333" />
            </svg>
        </p>

        @if(isset($returnUrl))
        <div class="btn-wrapper">
            <a target="_top"  href="{{$returnUrl}}" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                Zurück zur Webseite
            </a>
        </div>
        @endif

    </div>


@endsection
