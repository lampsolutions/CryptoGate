@extends('layouts.app2')


@section('content')

    <div class="mdl-card__supporting-text" style="text-align: center">
        <h4>Noch einen Schritt</h4>

        <p>Wir haben Ihnen gerade eine E-Mail</p>
        <p>an <b>{{ $email }}</b> geschickt. </p>

        <p></p>
        <p><b>Bitte bestätigen Sie die Adresse, indem Sie auf den in der E-Mail enthaltenen Link klicken.</b></p>

    </div>


@endsection
