<?php

/**
 * Variables recibidas desde forgotPassword.php:
 *
 * @var array $user
 * @var string $resetLink
 */

$name = $user["name"] ?? "";
$resetLink = $resetLink ?? "";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Restablecer contraseña - <?= htmlspecialchars(APP_NAME); ?>
    </title>

</head>

<body style="
    margin:0;
    padding:0;
    background:#f3f2ef;
    font-family:Montserrat, Arial, Helvetica, sans-serif;
    color:#010101;
">

    <!-- WRAPPER -->

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        role="presentation"
        style="
            width:100%;
            margin:0;
            padding:50px 20px;
            background:#f3f2ef;
        ">

        <tr>

            <td align="center">


                <!-- MAIN CARD -->

                <table
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    role="presentation"
                    style="
                        width:100%;
                        max-width:620px;
                        background:#ffffff;
                        border-radius:24px;
                        overflow:hidden;
                        box-shadow:0 12px 35px rgba(1,1,1,.08);
                    ">


                    <!-- BRAND HEADER -->

                    <tr>

                        <td
                            align="center"
                            style="
                                padding:36px 30px;
                                background:#ffffff;
                                border-bottom:1px solid #E5E5E5;
                            ">

                            <!-- Logo -->

                            <img
                                src="<?= APP_LOGO; ?>"
                                alt="Locally"
                                width="145"
                                style="
                                    display:block;
                                    width:145px;
                                    max-width:100%;
                                    height:auto;
                                    border:0;
                                ">

                        </td>

                    </tr>


                    <!-- TOP ACCENT -->

                    <tr>

                        <td
                            style="
                                height:6px;
                                background:#334d20;
                                font-size:0;
                                line-height:0;
                            ">

                            &nbsp;

                        </td>

                    </tr>


                    <!-- CONTENT -->

                    <tr>

                        <td
                            style="
                                padding:48px 50px 42px;
                            ">


                            <!-- SMALL LABEL -->

                            <p style="
                                margin:0 0 14px;
                                font-size:12px;
                                line-height:1.5;
                                font-weight:700;
                                letter-spacing:2px;
                                text-transform:uppercase;
                                color:#334d20;
                            ">

                                Account Security

                            </p>


                            <!-- TITLE -->

                            <h1 style="
                                margin:0 0 22px;
                                font-size:34px;
                                line-height:1.2;
                                font-weight:700;
                                letter-spacing:-.5px;
                                color:#0a3452;
                            ">

                                Restablecer contraseña

                            </h1>


                            <!-- GREETING -->

                            <p style="
                                margin:0 0 18px;
                                font-size:16px;
                                line-height:1.8;
                                color:#010101;
                            ">

                                Hola
                                <strong>
                                    <?= htmlspecialchars($name); ?>
                                </strong>,

                            </p>


                            <p style="
                                margin:0 0 18px;
                                font-size:15px;
                                line-height:1.8;
                                color:#555555;
                            ">

                                Recibimos una solicitud para restablecer
                                la contraseña de tu cuenta de
                                <strong style="color:#0a3452;">
                                    <?= htmlspecialchars(APP_NAME); ?>
                                </strong>.

                            </p>


                            <p style="
                                margin:0 0 30px;
                                font-size:15px;
                                line-height:1.8;
                                color:#555555;
                            ">

                                Para continuar, utiliza el botón que aparece
                                a continuación. El enlace es temporal y solo
                                puede utilizarse una vez.

                            </p>


                            <!-- CTA -->

                            <table
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                role="presentation">

                                <tr>

                                    <td align="center">

                                        <a
                                            href="<?= htmlspecialchars($resetLink); ?>"
                                            target="_blank"
                                            style="
                                                display:inline-block;
                                                padding:17px 34px;
                                                background:#334d20;
                                                color:#ffffff;
                                                text-decoration:none;
                                                border-radius:12px;
                                                font-family:Montserrat, Arial, Helvetica, sans-serif;
                                                font-size:15px;
                                                font-weight:700;
                                                line-height:1;
                                                letter-spacing:.2px;
                                            ">

                                            Restablecer contraseña

                                        </a>

                                    </td>

                                </tr>

                            </table>


                            <!-- EXPIRATION BOX -->

                            <table
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                role="presentation"
                                style="
                                    margin-top:34px;
                                ">

                                <tr>

                                    <td
                                        style="
                                            padding:18px 20px;
                                            background:#f7f7f5;
                                            border-left:4px solid #cec0b3;
                                            border-radius:10px;
                                        ">

                                        <p style="
                                            margin:0;
                                            font-size:13px;
                                            line-height:1.7;
                                            color:#555555;
                                        ">

                                            <strong style="color:#0a3452;">
                                                Importante:
                                            </strong>

                                            este enlace expirará en

                                            <strong>
                                                <?= TOKEN_EXPIRATION_MINUTES; ?>
                                                minutos
                                            </strong>

                                            por motivos de seguridad.

                                        </p>

                                    </td>

                                </tr>

                            </table>


                            <!-- FALLBACK LINK -->

                            <p style="
                                margin:32px 0 10px;
                                font-size:12px;
                                line-height:1.6;
                                color:#999999;
                            ">

                                Si el botón no funciona, copia y pega este
                                enlace en tu navegador:

                            </p>


                            <p style="
                                margin:0;
                                font-size:12px;
                                line-height:1.7;
                                word-break:break-all;
                            ">

                                <a
                                    href="<?= htmlspecialchars($resetLink); ?>"
                                    target="_blank"
                                    style="
                                        color:#0a3452;
                                        text-decoration:none;
                                    ">

                                    <?= htmlspecialchars($resetLink); ?>

                                </a>

                            </p>


                        </td>

                    </tr>


                    <!-- FOOTER -->

                    <tr>

                        <td
                            style="
                                padding:26px 40px 30px;
                                background:#0a3452;
                            ">

                            <p
                                style="
                                    margin:0 0 10px;
                                    text-align:center;
                                    font-size:13px;
                                    line-height:1.7;
                                    color:#ffffff;
                                ">

                                Si no solicitaste este cambio,
                                puedes ignorar este correo.

                            </p>


                            <p
                                style="
                                    margin:0;
                                    text-align:center;
                                    font-size:11px;
                                    line-height:1.6;
                                    color:rgba(255,255,255,.7);
                                ">

                                © <?= date("Y"); ?>

                                <?= htmlspecialchars(APP_NAME); ?>

                                · Todos los derechos reservados.

                            </p>

                        </td>

                    </tr>


                </table>

                <!-- END MAIN CARD -->


            </td>

        </tr>

    </table>

</body>

</html>