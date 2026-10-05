<?php

/**
 * Variables recibidas desde register.php:
 *
 * @var array $user
 */

$name = $user["name"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Bienvenido a <?= htmlspecialchars(APP_NAME); ?>
    </title>

</head>

<body style="
    margin:0;
    padding:0;
    background:#f3f2ef;
    font-family:Montserrat, Arial, Helvetica, sans-serif;
    color:#010101;
">

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        role="presentation"
        style="
        width:100%;
        background:#f3f2ef;
        padding:50px 20px;
    ">

        <tr>

            <td align="center">

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

                    <!-- LOGO -->

                    <tr>

                        <td
                            align="center"
                            style="
                            padding:36px 30px;
                            border-bottom:1px solid #E5E5E5;
                        ">

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

                    <!-- ACCENT -->

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

                        <td style="
                        padding:48px 50px;
                    ">

                            <p style="
                            margin:0 0 14px;
                            font-size:12px;
                            line-height:1.5;
                            font-weight:700;
                            letter-spacing:2px;
                            text-transform:uppercase;
                            color:#334d20;
                        ">

                                Welcome to Locally

                            </p>

                            <h1 style="
                            margin:0 0 22px;
                            font-size:34px;
                            line-height:1.2;
                            color:#0a3452;
                        ">

                                ¡Bienvenido, <?= htmlspecialchars($name); ?>!

                            </h1>

                            <p style="
                            margin:0 0 18px;
                            font-size:16px;
                            line-height:1.8;
                            color:#555555;
                        ">

                                Tu cuenta de
                                <strong style="color:#0a3452;">
                                    <?= htmlspecialchars(APP_NAME); ?>
                                </strong>
                                fue creada correctamente.

                            </p>

                            <p style="
                            margin:0 0 30px;
                            font-size:15px;
                            line-height:1.8;
                            color:#555555;
                        ">

                                Ya puedes iniciar sesión y comenzar a
                                administrar tu experiencia en Locally.

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
                                            href="<?= APP_URL; ?>/login.php"
                                            target="_blank"
                                            style="
                                            display:inline-block;
                                            padding:17px 34px;
                                            background:#334d20;
                                            color:#ffffff;
                                            text-decoration:none;
                                            border-radius:12px;
                                            font-size:15px;
                                            font-weight:700;
                                        ">

                                            Iniciar sesión

                                        </a>

                                    </td>

                                </tr>

                            </table>

                        </td>

                    </tr>

                    <!-- FOOTER -->

                    <tr>

                        <td
                            align="center"
                            style="
                            padding:26px 40px 30px;
                            background:#0a3452;
                        ">

                            <p style="
                            margin:0;
                            font-size:12px;
                            line-height:1.7;
                            color:#ffffff;
                        ">

                                Gracias por formar parte de
                                <?= htmlspecialchars(APP_NAME); ?>.

                            </p>

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

</body>

</html>