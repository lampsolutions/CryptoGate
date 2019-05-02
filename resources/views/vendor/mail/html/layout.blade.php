<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        table.wrapper {
            background-color: {{ $branding['branding_primary_color'] }};
            color: {{ $branding['branding_primary_text_color'] }};
        }

        td.header {
            box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 3px 1px -2px rgba(0,0,0,.2), 0 0px 0px 0 rgba(0,0,0,.12);
        }

        td.header a {
            color: {{ $branding['branding_primary_text_color'] }} !important;
        }

        .footer a {
            color: {{ $branding['branding_primary_text_color'] }} !important;
        }

        a.button-primary {
            background-color: {{ $branding['branding_secondary_color'] }} !important;
            color: {{ $branding['branding_secondary_text_color'] }} !important;
            border-top: 10px solid {{ $branding['branding_secondary_color'] }} !important;
            border-right: 18px solid {{ $branding['branding_secondary_color'] }} !important;
            border-bottom: 10px solid {{ $branding['branding_secondary_color'] }} !important;
            border-left: 18px solid {{ $branding['branding_secondary_color'] }} !important;
        }
    </style>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    {{ $header ?? '' }}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                                        {{ $subcopy ?? '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{ $footer ?? '' }}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
