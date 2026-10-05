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
    OBTENER PROPERTY ID
==================================================*/

$propertyId = filter_input(
    INPUT_POST,
    "property_id",
    FILTER_VALIDATE_INT
);


if (!$propertyId || $propertyId <= 0) {

    echo "property_not_found";
    exit;
}


/*==================================================
    OBTENER PROPIEDAD
    Verificamos que pertenezca al usuario
==================================================*/

$stmt = $conn->prepare("
    SELECT
        id,
        user_id
    FROM properties
    WHERE id = ?
    AND user_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $propertyId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();

$property = $result->fetch_assoc();

$stmt->close();


if (!$property) {

    echo "property_not_found";
    exit;
}


/*==================================================
    RECIBIR DATOS
==================================================*/

$name = trim($_POST["name"] ?? "");

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
    $_POST["bathrooms"] ?? 0,
    FILTER_VALIDATE_INT
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


$amenities = array_values(
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
    $canton === "" ||
    $price === "" ||
    $size === "" ||
    $sizeUnit === "" ||
    $propertyType === "" ||
    $description === "" ||
    $address === "" ||
    $latitude === "" ||
    $longitude === ""
) {

    echo "empty_fields";
    exit;
}


/* NOMBRE */

if (
    strlen($name) < 3 ||
    strlen($name) > 150
) {

    echo "invalid_name";
    exit;
}


/* PRECIO */

if (
    !is_numeric($price) ||
    (float) $price <= 0
) {

    echo "invalid_price";
    exit;
}


/* TAMAÑO */

if (
    !is_numeric($size) ||
    (float) $size <= 0
) {

    echo "invalid_size";
    exit;
}


/* BEDROOMS */

if (
    $bedrooms === false ||
    $bedrooms < 0 ||
    $bedrooms > 100
) {

    echo "invalid_bedrooms";
    exit;
}


/* BATHROOMS */

if (
    $bathrooms === false ||
    $bathrooms < 0 ||
    $bathrooms > 100
) {

    echo "invalid_bathrooms";
    exit;
}


/* UNIDAD */

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


/* TIPO */

if (
    !in_array(
        $propertyType,
        ["house", "lot", "apartment"],
        true
    )
) {

    echo "invalid_property_type";
    exit;
}


/* ESTADO */

if (
    !in_array(
        $status,
        ["active", "inactive"],
        true
    )
) {

    echo "invalid_status";
    exit;
}


/* DESCRIPCIÓN */

if (
    strlen($description) < 20 ||
    strlen($description) > 5000
) {

    echo "invalid_description";
    exit;
}


/* UBICACIÓN */

if (
    !is_numeric($latitude) ||
    !is_numeric($longitude) ||
    $latitude < -90 ||
    $latitude > 90 ||
    $longitude < -180 ||
    $longitude > 180
) {

    echo "invalid_location";
    exit;
}


$latitude =
    (float) $latitude;

$longitude =
    (float) $longitude;


/*==================================================
    IMÁGENES EXISTENTES
==================================================*/

$imageStmt = $conn->prepare("
    SELECT
        id,
        image
    FROM property_images
    WHERE property_id = ?
    ORDER BY id ASC
");


$imageStmt->bind_param(
    "i",
    $propertyId
);


$imageStmt->execute();

$imageResult =
    $imageStmt->get_result();


$currentImages =
    $imageResult->fetch_all(
        MYSQLI_ASSOC
    );


$imageStmt->close();


$currentImageCount =
    count($currentImages);


/*==================================================
    IMÁGENES A ELIMINAR
==================================================*/

$deletedImageIds =
    $_POST["deleted_images"] ?? [];


if (!is_array($deletedImageIds)) {

    $deletedImageIds = [];
}


$deletedImageIds =
    array_values(
        array_unique(
            array_map(
                "intval",
                $deletedImageIds
            )
        )
    );


/*==================================================
    VERIFICAR QUE LAS IMÁGENES
    PERTENEZCAN A ESTA PROPIEDAD
==================================================*/

$deletedImages = [];


if (!empty($deletedImageIds)) {

    $placeholders =
        implode(
            ",",
            array_fill(
                0,
                count($deletedImageIds),
                "?"
            )
        );


    $types =
        str_repeat(
            "i",
            count($deletedImageIds)
        );


    $sql = "
        SELECT
            id,
            image
        FROM property_images
        WHERE property_id = ?
        AND id IN ($placeholders)
    ";


    $stmt =
        $conn->prepare($sql);


    $params =
        [$propertyId];


    $paramTypes =
        "i";


    foreach ($deletedImageIds as $imageId) {

        $params[] =
            $imageId;

        $paramTypes .= "i";
    }


    $stmt->bind_param(
        $paramTypes,
        ...$params
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $deletedImages =
        $result->fetch_all(
            MYSQLI_ASSOC
        );


    $stmt->close();
}


/*==================================================
    NUEVAS IMÁGENES
==================================================*/

$files =
    $_FILES["images"] ?? null;


$validFiles = [];


if (
    $files &&
    isset($files["name"])
) {

    for (
        $i = 0;
        $i < count($files["name"]);
        $i++
    ) {

        if (
            $files["error"][$i] ===
            UPLOAD_ERR_NO_FILE
        ) {

            continue;
        }


        if (
            $files["error"][$i] !==
            UPLOAD_ERR_OK
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
}


/*==================================================
    MÁXIMO 4 IMÁGENES
==================================================*/

$newImageCount =
    count($validFiles);


$remainingImages =
    $currentImageCount -
    count($deletedImages);


if (
    $remainingImages +
    $newImageCount > 4
) {

    echo "too_many_images";
    exit;
}


/*==================================================
    VALIDAR NUEVAS IMÁGENES
==================================================*/

$allowedMimes = [

    "image/jpeg" => "jpg",

    "image/png" => "png",

    "image/webp" => "webp"

];


$finfo =
    finfo_open(
        FILEINFO_MIME_TYPE
    );


foreach ($validFiles as $file) {

    if (
        $file["size"] >
        5 * 1024 * 1024
    ) {

        finfo_close($finfo);

        echo "large_image";
        exit;
    }


    $mime =
        finfo_file(
            $finfo,
            $file["tmp_name"]
        );


    if (
        !isset(
            $allowedMimes[$mime]
        )
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
    TRANSACCIÓN
==================================================*/

$conn->begin_transaction();


$uploadedImages = [];


$deletedFiles = [];


try {


    /*==============================================
        ACTUALIZAR PROPIEDAD
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


    $stmt = $conn->prepare("
        UPDATE properties
        SET
            name = ?,
            province = ?,
            canton = ?,
            price = ?,
            size = ?,
            size_unit = ?,
            property_type = ?,
            bedrooms = ?,
            bathrooms = ?,
            amenities = ?,
            tags = ?,
            description = ?,
            status = ?,
            address = ?,
            latitude = ?,
            longitude = ?
        WHERE id = ?
        AND user_id = ?
    ");


    $stmt->bind_param(
        "sssddssiisssssddii",
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
        $longitude,
        $propertyId,
        $userId
    );


    $stmt->execute();

    $stmt->close();


    /*==============================================
        ELIMINAR REGISTROS DE IMÁGENES
    ==============================================*/

    if (!empty($deletedImages)) {

        $deleteStmt =
            $conn->prepare("
                DELETE FROM property_images
                WHERE id = ?
                AND property_id = ?
            ");


        foreach ($deletedImages as $image) {

            $deleteStmt->bind_param(
                "ii",
                $image["id"],
                $propertyId
            );


            $deleteStmt->execute();


            $deletedFiles[] =
                dirname(__DIR__) .
                "/uploads/properties/" .
                $image["image"];
        }


        $deleteStmt->close();
    }


    /*==============================================
        GUARDAR NUEVAS IMÁGENES
    ==============================================*/

    if (!empty($validFiles)) {

        $imageStmt =
            $conn->prepare("
                INSERT INTO property_images (
                    property_id,
                    image
                )
                VALUES (?, ?)
            ");


        foreach ($validFiles as $file) {

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


            $extension =
                $allowedMimes[$mime];


            $fileName =
                bin2hex(
                    random_bytes(16)
                ) .
                "." .
                $extension;


            $destination =
                $uploadDir .
                $fileName;


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


            $imageStmt->bind_param(
                "is",
                $propertyId,
                $fileName
            );


            $imageStmt->execute();
        }


        $imageStmt->close();
    }


    /*==============================================
        COMMIT
    ==============================================*/

    $conn->commit();


    /*==============================================
        ELIMINAR ARCHIVOS FÍSICOS
        DESPUÉS DEL COMMIT
    ==============================================*/

    foreach ($deletedFiles as $file) {

        if (file_exists($file)) {

            unlink($file);
        }
    }


    $conn->close();


    echo "success";

    exit;
} catch (Throwable $e) {


    /*==============================================
        ROLLBACK
    ==============================================*/

    $conn->rollback();


    /*==============================================
        ELIMINAR NUEVAS IMÁGENES
        QUE SÍ SE HABÍAN SUBIDO
    ==============================================*/

    foreach (
        $uploadedImages
        as $image
    ) {

        if (file_exists($image)) {

            unlink($image);
        }
    }


    error_log(
        "Update property error: " .
            $e->getMessage()
    );


    echo "server_error";


    $conn->close();

    exit;
}
