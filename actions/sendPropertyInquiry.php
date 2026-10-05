<?php

session_start();

require_once __DIR__ . "/../includes/conexion.php";
require_once __DIR__ . "/../includes/mail.php";
require_once __DIR__ . "/../includes/config.php";

header("Content-Type: text/plain");


/*==================================================
    VALIDAR REQUEST
==================================================*/

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    echo "invalid_request";
    exit;
}


/*==================================================
    ANTI-BOT - HONEYPOT
==================================================*/

$honeypot =
    trim(
        $_POST["website"] ?? ""
    );


if ($honeypot !== "") {

    echo "spam_detected";
    exit;
}


/*==================================================
    ANTI-BOT - TIEMPO
==================================================*/

$formTime =
    (int) (
        $_POST["form_time"] ?? 0
    );


$currentTime =
    time();


/*
 * Menos de 3 segundos =
 * comportamiento muy probablemente automatizado.
 */

if (
    !$formTime ||
    ($currentTime - $formTime) < 3
) {

    echo "spam_detected";
    exit;
}


/*==================================================
    RATE LIMIT BÁSICO
==================================================*/

if (
    isset($_SESSION["last_property_contact"])
) {

    $elapsed =
        $currentTime -
        (int) $_SESSION["last_property_contact"];


    if ($elapsed < 30) {

        echo "rate_limited";
        exit;
    }
}


/*==================================================
    DATOS
==================================================*/

$propertyId =
    filter_var(
        $_POST["property_id"] ?? null,
        FILTER_VALIDATE_INT
    );


$name =
    trim(
        $_POST["name"] ?? ""
    );


$email =
    trim(
        $_POST["email"] ?? ""
    );


$message =
    trim(
        $_POST["message"] ?? ""
    );


/*==================================================
    VALIDAR DATOS
==================================================*/

if (
    !$propertyId ||
    $propertyId <= 0
) {

    echo "invalid_property";
    exit;
}


if (
    $name === "" ||
    $email === "" ||
    $message === ""
) {

    echo "empty_fields";
    exit;
}


if (
    strlen($name) < 2 ||
    strlen($name) > 100
) {

    echo "invalid_request";
    exit;
}


if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    echo "invalid_email";
    exit;
}


if (
    strlen($message) < 10 ||
    strlen($message) > 2000
) {

    echo "invalid_message";
    exit;
}


/*==================================================
    OBTENER PROPIEDAD
==================================================*/

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        province,
        canton,
        price,
        property_type,
        bedrooms,
        bathrooms,
        size,
        size_unit,
        address
    FROM properties
    WHERE id = ?
    AND status = 'active'
    LIMIT 1
");


$stmt->bind_param(
    "i",
    $propertyId
);


$stmt->execute();


$result =
    $stmt->get_result();


$property =
    $result->fetch_assoc();


$stmt->close();


if (!$property) {

    echo "invalid_property";
    exit;
}


/*==================================================
    PREPARAR DATOS
==================================================*/

$propertyName =
    htmlspecialchars(
        $property["name"],
        ENT_QUOTES,
        "UTF-8"
    );


$visitorName =
    htmlspecialchars(
        $name,
        ENT_QUOTES,
        "UTF-8"
    );


$visitorEmail =
    htmlspecialchars(
        $email,
        ENT_QUOTES,
        "UTF-8"
    );


$visitorMessage =
    nl2br(
        htmlspecialchars(
            $message,
            ENT_QUOTES,
            "UTF-8"
        )
    );


$location =
    htmlspecialchars(
        $property["canton"] .
            ", " .
            $property["province"],
        ENT_QUOTES,
        "UTF-8"
    );


$price =
    number_format(
        (float) $property["price"],
        2
    );


$size =
    htmlspecialchars(
        $property["size"],
        ENT_QUOTES,
        "UTF-8"
    );


$sizeUnit =
    $property["size_unit"] === "sqft"
    ? "sqft"
    : "m²";


$propertyType =
    htmlspecialchars(
        ucfirst(
            $property["property_type"]
        ),
        ENT_QUOTES,
        "UTF-8"
    );


/*==================================================
    EMAIL HTML
==================================================*/

$emailHtml = "

<!DOCTYPE html>

<html lang='en'>

<head>

<meta charset='UTF-8'>

<meta name='viewport'
      content='width=device-width, initial-scale=1.0'>

</head>

<body style='
    margin:0;
    padding:0;
    background:#f5f5f5;
    font-family:Arial,Helvetica,sans-serif;
    color:#222;
'>

<table
    width='100%'
    cellpadding='0'
    cellspacing='0'
    style='
        background:#f5f5f5;
        padding:35px 15px;
    '>

<tr>

<td align='center'>

<table
    width='100%'
    cellpadding='0'
    cellspacing='0'
    style='
        max-width:620px;
        background:#ffffff;
        border-radius:18px;
        overflow:hidden;
        box-shadow:0 8px 30px rgba(0,0,0,.08);
    '>

<!-- HEADER -->

<tr>

<td
    style='
        padding:30px;
        background:#334d20;
        text-align:center;
    '>

<img
    src='https://locally.cr/img/blackLogo.png'
    width='150'
    alt='Locally'
    style='
        display:block;
        margin:0 auto;
        max-width:150px;
    '>

</td>

</tr>


<!-- TITLE -->

<tr>

<td
    style='
        padding:35px 35px 20px;
    '>

<h1 style='
    margin:0;
    font-size:26px;
    color:#0a3452;
'>

New Property Inquiry

</h1>

<p style='
    margin:10px 0 0;
    color:#777;
    font-size:14px;
'>

A visitor submitted a contact request from your website.

</p>

</td>

</tr>


<!-- PROPERTY -->

<tr>

<td style='padding:10px 35px 25px;'>

<table
    width='100%'
    cellpadding='0'
    cellspacing='0'
    style='
        background:#f7f7f7;
        border-radius:14px;
    '>

<tr>

<td style='padding:22px;'>

<h2 style='
    margin:0 0 12px;
    color:#334d20;
    font-size:20px;
'>

{$propertyName}

</h2>


<p style='
    margin:6px 0;
    font-size:14px;
    color:#555;
'>

📍 {$location}

</p>


<p style='
    margin:6px 0;
    font-size:14px;
    color:#555;
'>

Property type: {$propertyType}

</p>


<p style='
    margin:6px 0;
    font-size:14px;
    color:#555;
'>

{$property["bedrooms"]} bedrooms ·
{$property["bathrooms"]} bathrooms ·
{$size} {$sizeUnit}

</p>


<p style='
    margin:10px 0 0;
    font-size:17px;
    font-weight:700;
    color:#0a3452;
'>

\${$price}

</p>

</td>

</tr>

</table>

</td>

</tr>


<!-- CONTACT -->

<tr>

<td style='padding:5px 35px 25px;'>

<h2 style='
    margin:0 0 15px;
    color:#0a3452;
    font-size:19px;
'>

Contact Information

</h2>


<p style='
    margin:7px 0;
    font-size:15px;
'>

<strong>Name:</strong>
{$visitorName}

</p>


<p style='
    margin:7px 0;
    font-size:15px;
'>

<strong>Email:</strong>

<a
    href='mailto:{$visitorEmail}'
    style='color:#0a3452;'>

{$visitorEmail}

</a>

</p>

</td>

</tr>


<!-- MESSAGE -->

<tr>

<td style='padding:5px 35px 35px;'>

<h2 style='
    margin:0 0 15px;
    color:#0a3452;
    font-size:19px;
'>

Message

</h2>


<div style='
    background:#f7f7f7;
    border-left:4px solid #334d20;
    padding:18px;
    border-radius:8px;
    font-size:15px;
    line-height:1.7;
    color:#444;
'>

{$visitorMessage}

</div>

</td>

</tr>


<!-- FOOTER -->

<tr>

<td
    style='
        padding:22px 35px;
        background:#fafafa;
        text-align:center;
    '>

<p style='
    margin:0;
    color:#999;
    font-size:12px;
'>

Locally Costa Rica · Property Inquiry

</p>

<p style='
    margin:7px 0 0;
    color:#999;
    font-size:12px;
'>

Property ID: {$property["id"]}

</p>

</td>

</tr>

</table>

</td>

</tr>

</table>

</body>

</html>
";


/*==================================================
    TEXT VERSION
==================================================*/

$altBody =

    "New Property Inquiry\n\n" .

    "Property: " .
    $property["name"] .
    "\n" .

    "Location: " .
    $property["canton"] .
    ", " .
    $property["province"] .
    "\n\n" .

    "Name: " .
    $name .
    "\n" .

    "Email: " .
    $email .
    "\n\n" .

    "Message:\n" .
    $message;


/*==================================================
    SEND EMAIL
==================================================*/

$sent =
    sendMail(
        "sales@locally.cr",
        "New inquiry - " . $property["name"],
        $emailHtml,
        $altBody,
        $email,
        $name
    );


if (!$sent) {

    echo "mail_error";
    exit;
}


/*==================================================
    RATE LIMIT
==================================================*/

$_SESSION["last_property_contact"] = time();


echo "success";

exit;
