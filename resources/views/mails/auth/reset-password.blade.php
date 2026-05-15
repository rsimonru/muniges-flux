@extends('mails.template')

@section('content')
    <table class="email-content" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td>
                <p> Hola {{ $user->name }}.</p>
                <p> Estás recibiendo este correo porque has solicitado restablecer tu contraseña de acceso a Muniges. Haz click en el enlace de más abajo, para indicar tu nueva contraseña.</p>
                <p style="text-align: center;">
                    <a style="text-decoration: underline;font-weight:bold" href="{{ $url }}">Cambiar contraseña</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
