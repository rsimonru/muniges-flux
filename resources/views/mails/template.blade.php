<!DOCTYPE html>
<html>

<head>
    <title>Muniges</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #dddddd;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
        }

        .email-header {
            padding: 20px;
            text-align: left;
            background-color: white;
        }

        .email-header img {
            max-width: 100px;
        }

        .email-content {
            padding: 20px;
            background-color: #ffffff;
        }

        .email-footer {
            padding: 5px 20px;
            text-align: center;
            background-color: #f4f4f4;
        }

        .order-lines-table th {
            background-color: #f4f4f4;
        }

        .line {
            background-color: #f4f4f4;
        }

        p {
            margin: 5px 0;
        }

        a {
            /* text-decoration: none; */
            color: black
        }
    </style>
</head>

<body>
    <table class="email-container" align="center" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td>
                <table class="email-header" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="right" valign="middle">
                            <img src="{{ url('/storage/aytos/'.$townhall_id.'/logo_peq.png') }}" width="50" class="mt-1" />
                        </td>
                        <td align="left" valign="middle">
                            {{ $townhall_data['name'] }}
                        </td>
                    </tr>
                </table>
                @yield('content')
                <table class="line" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="padding: 5px 0;">
                            <div style="margin: 0 20px; border-top: 1px solid #9b9b9b;"></div>
                        </td>
                    </tr>
                </table>

                <table class="email-footer" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td>
                            <p style="margin-top: 30px;font-size:10px;color:grey;text-align:justify">
                                Este mensaje se dirige exclusivamente a su destinatario y puede contener información CONFIDENCIAL sometida a secreto profesional o cuya divulgación este prohibida en virtud de la legislación
                                vigente. Si ha recibido este mensaje por error, le rogamos que nos lo comunique inmediatamente por esta misma vía y proceda a su destrucción.
                                (This message is intended
                                exclusively for its address and may contain information that is CONFIDENTIAL and protected by a professional privilege or whose disclosure is prohibited by law. If this
                                message has been received in error, please immediately notify us via e-mail and delete it.)
                            </p>
                        </td>
                    </tr>
                </table>


                <table class="line" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="padding: 5px 0;">
                            <div style="margin: 0 20px; border-top: 1px solid #9b9b9b;"></div>
                        </td>
                    </tr>
                </table>

                <table class="email-footer" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="text-align: left">
                            <p>© {{ today()->year }} Muniges.</p>
                            <p style="text-align: left">
                                <a href="#">Política de privacidad</a> |
                                <a href="#">Política de cookies</a> |
                                <a href="#">Aviso legal</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

</body>

</html>
