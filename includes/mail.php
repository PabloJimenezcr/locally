<?php

require_once __DIR__ . "/config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =========================
// PHPMailer
// =========================
require_once __DIR__ . "/PHPMailer/src/Exception.php";
require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";

/**
 * Envía un correo HTML.
 *
 * @param string $to      Destinatario
 * @param string $subject Asunto
 * @param string $body    Contenido HTML
 * @param string|null $altBody Contenido alternativo en texto plano
 *
 * @return bool
 */
function sendMail(
    $to,
    $subject,
    $body,
    $altBody = null,
    $replyTo = null,
    $replyName = null
) {
    $mail = new PHPMailer(true);

    try {

        // =========================
        // CONFIGURACIÓN SMTP
        // =========================

        $mail->isSMTP();


        $mail->Host = "gtxm1026.siteground.biz";

        $mail->SMTPAuth = true;

        $mail->Username = "noreply@locally.cr";

        $mail->Password = "bc1yi}324l@C";

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->Port = 465;

        // =========================
        // CONFIGURACIÓN GENERAL
        // =========================

        $mail->CharSet = "UTF-8";

        $mail->setFrom(
            APP_EMAIL,
            APP_NAME
        );

        $mail->addAddress($to);

        if (!empty($replyTo)) {

            $mail->addReplyTo(
                $replyTo,
                $replyName ?? ""
            );
        }

        $mail->isHTML(true);

        $mail->Subject = $subject;

        $mail->Body = $body;

        $mail->AltBody = $altBody ?? strip_tags($body);

        $mail->send();

        return true;
    } catch (Exception $e) {

        error_log(
            "PHPMailer: " . $mail->ErrorInfo
        );

        return false;
    }
}
