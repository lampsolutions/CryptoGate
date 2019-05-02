@component('mail::message')
# E-Mail Adresse Bestätigung

zur Verifizierung Ihrer E-Mail Adresse klicken Sie bitte auf folgenden Link:
@component('mail::button', ['url' => $url])
    E-Mail Adresse bestätigen
@endcomponent


Vielen Dank,<br>
{{ $branding['branding_seller_name']  }}
@endcomponent