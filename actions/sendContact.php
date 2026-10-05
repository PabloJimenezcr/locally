<?php

session_start();

require_once "../includes/config.php";
require_once "../includes/conexion.php";
require_once "../includes/mail.php";


/* =========================================================
   SOLO POST
========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    exit("server_error");
}


/* =========================================================
   ANTI-SPAM - HONEYPOT
========================================================= */

$website = trim(
    $_POST["website"] ?? ""
);

if ($website !== "") {

    exit("spam_detected");
}


/* =========================================================
   RATE LIMIT
========================================================= */

$now = time();

if (
    isset($_SESSION["contact_last_sent"]) &&
    ($now - $_SESSION["contact_last_sent"]) < 30
) {

    exit("rate_limited");
}


/* =========================================================
   FORM TIME
========================================================= */

$formTime = filter_input(
    INPUT_POST,
    "form_time",
    FILTER_VALIDATE_INT
);


if (
    !$formTime ||
    ($now - $formTime) < 3
) {

    exit("spam_detected");
}


/* =========================================================
   DATOS
========================================================= */

$name = trim(
    $_POST["name"] ?? ""
);

$email = trim(
    $_POST["email"] ?? ""
);

$phone = trim(
    $_POST["phone"] ?? ""
);

$message = trim(
    $_POST["message"] ?? ""
);


/* =========================================================
   VALIDACIONES
========================================================= */

if (
    $name === "" ||
    $email === "" ||
    $message === ""
) {

    exit("invalid_data");
}


if (
    mb_strlen($name) < 3 ||
    mb_strlen($name) > 100
) {

    exit("invalid_data");
}


if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    exit("invalid_data");
}


if (mb_strlen($email) > 150) {

    exit("invalid_data");
}


if (
    mb_strlen($message) < 10 ||
    mb_strlen($message) > 1500
) {

    exit("invalid_data");
}


if (
    $phone !== "" &&
    !preg_match(
        '/^[0-9+\-\s()]{7,30}$/',
        $phone
    )
) {

    exit("invalid_data");
}


/* =========================================================
   ESCAPAR DATOS PARA HTML
========================================================= */

$safeName =
    htmlspecialchars(
        $name,
        ENT_QUOTES,
        "UTF-8"
    );


$safeEmail =
    htmlspecialchars(
        $email,
        ENT_QUOTES,
        "UTF-8"
    );


$safePhone =
    $phone !== ""
    ? htmlspecialchars(
        $phone,
        ENT_QUOTES,
        "UTF-8"
    )
    : "Not provided";


$safeMessage =
    nl2br(
        htmlspecialchars(
            $message,
            ENT_QUOTES,
            "UTF-8"
        )
    );


/* =========================================================
   EMAIL
========================================================= */

$subject =
    "New contact message - Locally";


$body = "

<!DOCTYPE html>

<html lang='en'>

<head>

<meta charset='UTF-8'>

<meta name='viewport'
content='width=device-width, initial-scale=1.0'>

</head>


<body
style='margin:0;
padding:0;
background:#f5f6f3;
font-family:Arial,Helvetica,sans-serif;'>


<table
width='100%'
cellpadding='0'
cellspacing='0'
style='background:#f5f6f3;
padding:40px 20px;'>


<tr>

<td align='center'>


<table
width='600'
cellpadding='0'
cellspacing='0'
style='max-width:600px;
background:#ffffff;
border-radius:16px;
overflow:hidden;'>


<!-- HEADER -->

<tr>

<td
style='background:#334d20;
padding:30px;
text-align:center;'>
    
<img
src='" . APP_LOGO . "'
alt='Locally'
style='max-width:150px;
height:auto;'>

</td>

</tr>


<!-- CONTENT -->

<tr>

<td
style='padding:40px;'>


<h1
style='margin:0 0 10px;
font-size:24px;
color:#334d20;'>
    
New Contact Message

</h1>


<p
style='margin:0 0 30px;
font-size:15px;
line-height:1.6;
color:#666;'>

Someone has contacted Locally through
the website contact form.

</p>


<!-- CONTACT -->

<table
width='100%'
cellpadding='0'
cellspacing='0'
style='font-size:14px;'>


<tr>

<td
style='padding:10px 0;
font-weight:bold;
color:#334d20;
width:120px;'>

Name

</td>

<td
style='padding:10px 0;
color:#333;'>

" . $safeName . "

</td>

</tr>


<tr>

<td
style='padding:10px 0;
font-weight:bold;
color:#334d20;'>

Email

</td>

<td
style='padding:10px 0;
color:#333;'>

" . $safeEmail . "

</td>

</tr>


<tr>

<td
style='padding:10px 0;
font-weight:bold;
color:#334d20;'>

Phone

</td>

<td
style='padding:10px 0;
color:#333;'>

" . $safePhone . "

</td>

</tr>


</table>


<!-- MESSAGE -->

<div
style='margin-top:30px;
padding:20px;
background:#f7f8f5;
border-radius:12px;'>

<p
style='margin:0 0 10px;
font-weight:bold;
color:#334d20;'>

Message

</p>


<p
style='margin:0;
font-size:14px;
line-height:1.7;
color:#333;'>

" . $safeMessage . "

</p>

</div>


</td>

</tr>


<!-- FOOTER -->

<tr>

<td
style='padding:20px 40px;
background:#fafafa;
text-align:center;
font-size:12px;
color:#888;'>

Locally Costa Rica<br>

<a
href='mailto:" . APP_EMAIL . "'
style='color:#334d20;
text-decoration:none;'>

" . APP_EMAIL . "

</a>

</td>

</tr>


</table>

</td>

</tr>

</table>

</body>

</html>
";


/* =========================================================
   ENVIAR
========================================================= */

/* =========================================================
   ENVIAR
========================================================= */

$sent = sendMail(
    "sales@locally.cr",
    $subject,
    $body,
    "
New Contact Message

Name: {$name}
Email: {$email}
Phone: {$phone}

Message:
{$message}
",
    $email,
    $name
);


if (!$sent) {

    exit("mail_error");
}


/* =========================================================
   REGISTRAR ENVÍO
========================================================= */

$_SESSION["contact_last_sent"] = $now;


exit("success");
