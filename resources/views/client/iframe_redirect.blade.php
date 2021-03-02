@extends('layouts.app2')

@section('template_title')
    See Message
@endsection

@section('head')
@endsection

@section('content')

    <h3>Redirecting ...</h3>

    <script type="text/javascript">
        window.top.location = "{!! $return_url !!}";
    </script>

@endsection
