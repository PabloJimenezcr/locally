<?php

session_start();

require_once __DIR__ . "/../includes/conexion.php";

header("Content-Type: text/plain");


/*==================================================
    VALIDAR REQUEST
==================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo "invalid_request";
    exit;
}


if (!isset($_SESSION["id"])) {

    echo "not_logged";
    exit;
}


$userId = (int) $_SESSION["id"];


/*==================================================
    OBTENER DATOS
==================================================*/

$name =
    trim($_POST["name"] ?? "");

$province =
    trim($_POST["province"] ?? "");

$canton =
    trim($_POST["canton"] ?? "");

$price =
    $_POST["price"] ?? "";

$size =
    $_POST["size"] ?? "";

$sizeUnit =
    $_POST["size_unit"] ?? "";

$propertyType =
    $_POST["property_type"] ?? "";


$bedrooms = filter_var(
    $_POST["bedrooms"] ?? 0,
    FILTER_VALIDATE_INT
);


$bathrooms = filter_var(
    $_POST["bathrooms"] ?? "",
    FILTER_VALIDATE_FLOAT
);

$description =
    trim($_POST["description"] ?? "");


$status =
    $_POST["status"] ?? "active";


$address =
    trim($_POST["address"] ?? "");


$latitude =
    $_POST["latitude"] ?? "";

$longitude =
    $_POST["longitude"] ?? "";


/*==================================================
    AMENITIES
==================================================*/

$amenities =
    $_POST["amenities"] ?? [];


if (!is_array($amenities)) {

    $amenities = [];
}


$allowedAmenities = [

    "parking",
    "wifi",
    "elevator"

];


$amenities =
    array_values(
        array_intersect(
            $amenities,
            $allowedAmenities
        )
    );


/*==================================================
    TAGS
==================================================*/

$tags = [];


if (!empty($_POST["tags"])) {

    $decodedTags =
        json_decode(
            $_POST["tags"],
            true
        );


    if (is_array($decodedTags)) {

        foreach ($decodedTags as $tag) {

            $tag =
                trim((string) $tag);


            if (
                $tag !== "" &&
                strlen($tag) <= 50
            ) {

                $tags[] = $tag;
            }
        }
    }
}


/*==================================================
    VALIDACIONES
==================================================*/

if (

    $name === "" ||
    $province === "" ||
    $price === "" ||
    $size === "" ||
    $sizeUnit === "" ||
    $propertyType === "" ||
    $description === "" ||
    $address === "" ||
    $latitude === "" ||
    $longitude === "" ||
    $canton === ""

) {

    echo "empty_fields";
    exit;
}


/*==================================================
    NOMBRE
==================================================*/

if (

    strlen($name) < 3 ||
    strlen($name) > 150

) {

    echo "invalid_name";
    exit;
}


/*==================================================
    PRECIO
==================================================*/

if (

    !is_numeric($price) ||
    (float) $price <= 0

) {

    echo "invalid_price";
    exit;
}


/*==================================================
    TAMAÑO
==================================================*/

if (

    !is_numeric($size) ||
    (float) $size <= 0

) {

    echo "invalid_size";
    exit;
}


/*==================================================
    BEDROOMS
==================================================*/

if (

    $bedrooms === false ||
    $bedrooms < 0 ||
    $bedrooms > 100

) {

    echo "invalid_bedrooms";
    exit;
}


/*==================================================
    BATHROOMS
==================================================*/

if (

    $bathrooms === false ||
    $bathrooms < 0 ||
    $bathrooms > 100

) {

    echo "invalid_bathrooms";
    exit;
}


/*==================================================
    UNIDAD
==================================================*/

if (

    !in_array(
        $sizeUnit,
        ["m2", "sqft"],
        true
    )

) {

    echo "invalid_size_unit";
    exit;
}


/*==================================================
    TIPO
==================================================*/

if (

    !in_array(
        $propertyType,
        [
            "house",
            "lot",
            "apartment"
        ],
        true
    )

) {

    echo "invalid_property_type";
    exit;
}


/*==================================================
    ESTADO
==================================================*/

if (

    !in_array(
        $status,
        [
            "active",
            "inactive"
        ],
        true
    )

) {

    echo "invalid_status";
    exit;
}


/*==================================================
    DESCRIPCIÓN
==================================================*/

if (

    strlen($description) < 20 ||
    strlen($description) > 5000

) {

    echo "invalid_description";
    exit;
}


/*==================================================
    COORDENADAS
==================================================*/

if (

    !is_numeric($latitude) ||
    !is_numeric($longitude)

) {

    echo "invalid_location";
    exit;
}


$latitude =
    (float) $latitude;

$longitude =
    (float) $longitude;


/*==================================================
    VALIDAR IMÁGENES
==================================================*/

$files =
    $_FILES["images"] ?? null;

$validFiles = [];


/*----------------------------------------------
    DEBE EXISTIR LA GALERÍA
----------------------------------------------*/

if (
    !$files ||
    !isset($files["name"])
) {

    echo "primary_image_required";
    exit;
}


/*----------------------------------------------
    MÁXIMO 7 ARCHIVOS
----------------------------------------------*/

$totalFiles =
    count($files["name"]);


if ($totalFiles > 7) {

    echo "too_many_images";
    exit;
}


/*----------------------------------------------
    PRIMERA IMAGEN OBLIGATORIA
----------------------------------------------*/

if (

    !isset($files["error"][0]) ||
    $files["error"][0] === UPLOAD_ERR_NO_FILE

) {

    echo "primary_image_required";
    exit;
}


/*----------------------------------------------
    PROCESAR ARCHIVOS
----------------------------------------------*/

for (

    $i = 0;
    $i < $totalFiles;
    $i++

) {

    /* Slot vacío */

    if (
        $files["error"][$i] === UPLOAD_ERR_NO_FILE
    ) {

        continue;
    }


    /* Error de subida */

    if (
        $files["error"][$i] !== UPLOAD_ERR_OK
    ) {

        echo "upload_error";
        exit;
    }


    $validFiles[] = [

        "name" =>
        $files["name"][$i],

        "tmp_name" =>
        $files["tmp_name"][$i],

        "size" =>
        $files["size"][$i],

        "error" =>
        $files["error"][$i]

    ];
}


/*----------------------------------------------
    SEGURIDAD ADICIONAL
----------------------------------------------*/

if (
    count($validFiles) === 0
) {

    echo "primary_image_required";
    exit;
}


if (
    count($validFiles) > 7
) {

    echo "too_many_images";
    exit;
}


/*==================================================
    VALIDAR MIME Y TAMAÑO
==================================================*/

$allowedMimes = [

    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"

];


$finfo =
    finfo_open(FILEINFO_MIME_TYPE);


if (!$finfo) {

    echo "server_error";
    exit;
}


foreach ($validFiles as $file) {

    /*------------------------------------------
        TAMAÑO
    ------------------------------------------*/

    if (
        $file["size"] > 5 * 1024 * 1024
    ) {

        finfo_close($finfo);

        echo "large_image";
        exit;
    }


    /*------------------------------------------
        MIME REAL
    ------------------------------------------*/

    $mime =
        finfo_file(
            $finfo,
            $file["tmp_name"]
        );


    if (
        !isset($allowedMimes[$mime])
    ) {

        finfo_close($finfo);

        echo "invalid_image";
        exit;
    }
}


finfo_close($finfo);


/*==================================================
    DIRECTORIO
==================================================*/

$uploadDir =
    dirname(__DIR__) .
    "/uploads/properties/";


if (!is_dir($uploadDir)) {

    if (
        !mkdir(
            $uploadDir,
            0755,
            true
        )
    ) {

        echo "server_error";
        exit;
    }
}


/*==================================================
    INICIAR TRANSACCIÓN
==================================================*/

$conn->begin_transaction();

$uploadedImages = [];


try {


    /*==============================================
        JSON
    ==============================================*/

    $amenitiesJson =
        json_encode(
            $amenities,
            JSON_UNESCAPED_UNICODE
        );


    $tagsJson =
        json_encode(
            $tags,
            JSON_UNESCAPED_UNICODE
        );


    if (
        $amenitiesJson === false ||
        $tagsJson === false
    ) {

        throw new Exception(
            "JSON encoding failed."
        );
    }


    /*==============================================
        INSERTAR PROPIEDAD
    ==============================================*/

    $stmt = $conn->prepare("

        INSERT INTO properties (

            user_id,
            name,
            province,
            canton,
            price,
            size,
            size_unit,
            property_type,
            bedrooms,
            bathrooms,
            amenities,
            tags,
            description,
            status,
            address,
            latitude,
            longitude

        )

        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?
        )

    ");


    $stmt->bind_param(

        "isssddssidsssssdd",
        $userId,
        $name,
        $province,
        $canton,
        $price,
        $size,
        $sizeUnit,
        $propertyType,
        $bedrooms,
        $bathrooms,
        $amenitiesJson,
        $tagsJson,
        $description,
        $status,
        $address,
        $latitude,
        $longitude

    );


    $stmt->execute();


    $propertyId =
        $stmt->insert_id;


    $stmt->close();


    /*==============================================
        GUARDAR IMÁGENES
    ==============================================*/

    $imageStmt = $conn->prepare("

        INSERT INTO property_images (

            property_id,
            image

        )

        VALUES (?, ?)

    ");


    foreach (
        $validFiles as $file
    ) {


        /*------------------------------------------
            MIME
        ------------------------------------------*/

        $finfo =
            finfo_open(
                FILEINFO_MIME_TYPE
            );


        $mime =
            finfo_file(
                $finfo,
                $file["tmp_name"]
            );


        finfo_close($finfo);


        /*------------------------------------------
            EXTENSIÓN
        ------------------------------------------*/

        $extension =
            $allowedMimes[$mime];


        /*------------------------------------------
            NOMBRE SEGURO
        ------------------------------------------*/

        $fileName =
            bin2hex(
                random_bytes(16)
            )
            . "."
            . $extension;


        /*------------------------------------------
            DESTINO
        ------------------------------------------*/

        $destination =
            $uploadDir .
            $fileName;


        /*------------------------------------------
            MOVER ARCHIVO
        ------------------------------------------*/

        if (
            !move_uploaded_file(
                $file["tmp_name"],
                $destination
            )
        ) {

            throw new Exception(
                "Image upload failed."
            );
        }


        $uploadedImages[] =
            $destination;


        /*------------------------------------------
            INSERTAR EN BD
        ------------------------------------------*/

        $imageStmt->bind_param(

            "is",

            $propertyId,
            $fileName

        );


        $imageStmt->execute();
    }


    $imageStmt->close();


    /*==============================================
        CONFIRMAR
    ==============================================*/

    $conn->commit();

    $conn->close();

    echo "success";

    exit;
} catch (Throwable $e) {


    /*==============================================
        ROLLBACK BD
    ==============================================*/

    $conn->rollback();


    /*==============================================
        ELIMINAR ARCHIVOS
    ==============================================*/

    foreach (
        $uploadedImages as $image
    ) {

        if (
            file_exists($image)
        ) {

            unlink($image);
        }
    }


    /*==============================================
        LOG
    ==============================================*/

    error_log(
        "Create property error: " .
            $e->getMessage()
    );


    echo "server_error";


    $conn->close();

    exit;
}
