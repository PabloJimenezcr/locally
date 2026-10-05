<?php

session_start();

require_once "includes/conexion.php";


/*==================================================
    OBTENER ID DE LA PROPIEDAD
==================================================*/

$propertyId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);


if (!$propertyId || $propertyId <= 0) {

    header("Location: dashboard.php");
    exit;
}


/*==================================================
    OBTENER PROPIEDAD
==================================================*/

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.user_id,
        p.name,
        p.province,
        p.canton,
        p.price,
        p.size,
        p.size_unit,
        p.property_type,
        p.bedrooms,
        p.bathrooms,
        p.amenities,
        p.tags,
        p.description,
        p.status,
        p.address,
        p.latitude,
        p.longitude,
        p.created_at,

        u.name AS author_name,
        u.email AS author_email,
        u.photo AS author_photo

    FROM properties p

    INNER JOIN users u
        ON u.id = p.user_id

    WHERE p.id = ?

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


/*==================================================
    PROPIEDAD NO EXISTE
==================================================*/

if (!$property) {

    header("Location: dashboard.php");
    exit;
}

/*==================================================
    FOTO DEL AUTOR
==================================================*/

$authorPhoto =
    !empty($property["author_photo"])
    ? "uploads/profiles/" . $property["author_photo"]
    : "img/mari.jpg";

/*==================================================
    AMENITIES
==================================================*/

$amenities = [];


if (!empty($property["amenities"])) {

    $decodedAmenities =
        json_decode(
            $property["amenities"],
            true
        );


    if (is_array($decodedAmenities)) {

        $amenities =
            $decodedAmenities;
    }
}


/*==================================================
    TAGS
==================================================*/

$tags = [];


if (!empty($property["tags"])) {

    $decodedTags =
        json_decode(
            $property["tags"],
            true
        );


    if (is_array($decodedTags)) {

        $tags =
            $decodedTags;
    }
}


/*==================================================
    IMÁGENES
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


$propertyImages =
    $imageResult->fetch_all(
        MYSQLI_ASSOC
    );


$imageStmt->close();


/*==================================================
    DATOS AUXILIARES
==================================================*/

$sizeUnit =
    $property["size_unit"] === "sqft"
    ? "sqft"
    : "m²";


$propertyTypeLabels = [

    "house" =>
    "House",

    "lot" =>
    "Lot",

    "apartment" =>
    "Apartment"

];


$propertyType =
    $propertyTypeLabels[$property["property_type"]]
    ?? $property["property_type"];


/*==================================================
    PERMISOS DE EDICIÓN
==================================================*/

$isLogged =
    isset($_SESSION["id"]);


$isOwner =
    $isLogged &&
    (int) $_SESSION["id"] ===
    (int) $property["user_id"];


$isAdmin =
    $isLogged &&
    isset($_SESSION["role"]) &&
    strtolower(
        $_SESSION["role"]
    ) === "admin";


$canEdit =
    $isOwner || $isAdmin;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="css/property.css">

    <link
        rel="stylesheet"
        href="css/addProperty.css">

    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Locally</title>
</head>

<body>



    <nav class="mobile-nav d-md-none" aria-label="Mobile navigation">

        <a href="dashboard.php" class="nav-item" aria-current="page">
            <i class="ri-home-5-line" aria-hidden="true"></i>
            <span>Home</span>
        </a>

        <a href="addProperty.php" class="nav-item">
            <i class="ri-add-circle-line" aria-hidden="true"></i>
            <span>Add</span>
        </a>

        <a href="user.php" class="nav-item">
            <i class="ri-user-3-line" aria-hidden="true"></i>
            <span>Account</span>
        </a>

        <a href="actions/logout.php" class="nav-item">
            <i class="ri-logout-box-line" aria-hidden="true"></i>
            <span>Log out</span>

        </a>

    </nav>


    <header class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <button
                type="button"
                class="btn-back"
                onclick="history.back()"
                aria-label="Go back">

                <i
                    class="ri-arrow-left-line"
                    aria-hidden="true">
                </i>

                <span>Back</span>

            </button>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown avatar-dropdown">

                    <button class="avatar border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Open profile menu">

                        <img
                            src="uploads/profiles/<?= htmlspecialchars($_SESSION["photo"]); ?>"
                            class="img-fluid"
                            alt="Foto de perfil">


                    </button>

                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown dropdown-menu-lg-end">

                        <li class="dropdown-header">
                            <strong>Pablo</strong>
                            <small>Admin</small>
                        </li>

                        <li>
                            <a class="dropdown-item" href="user.php">
                                <i class="ri-user-3-line" aria-hidden="true"></i>
                                Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item logout" href="actions/logout.php">
                                <i class="ri-logout-box-line" aria-hidden="true"></i>
                                Log out
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </header>






    <section class="container pName pt-5">
        <div class="d-flex justify-content-between align-items-center">

            <h1 class="mb-0">

                <?= htmlspecialchars(
                    $property["name"]
                ); ?>

            </h1>



            <?php if ($canEdit): ?>

                <a
                    href="editProperty.php?id=<?= (int) $property["id"]; ?>"
                    class="btn-edit-property">

                    <i
                        class="ri-pencil-line"
                        aria-hidden="true">
                    </i>

                    Edit Property

                </a>

            <?php endif; ?>

        </div>


    </section>

    <section
        class="gallery-slider container py-4"
        aria-label="Property image gallery">

        <div class="main-slider position-relative">

            <button
                class="nav-btn prev"
                type="button"
                aria-label="Imagen anterior">

                <i
                    class="ri-arrow-left-s-line"
                    aria-hidden="true">
                </i>

            </button>


            <?php

            $mainImage =
                !empty($propertyImages)
                ? "uploads/properties/" .
                $propertyImages[0]["image"]
                : "img/cardHome.jpg";

            ?>

            <img
                id="mainImage"
                src="<?= htmlspecialchars($mainImage); ?>"
                alt="<?= htmlspecialchars($property["name"]); ?>"
                class="img-fluid main-img fade-effect"
                loading="eager"
                decoding="async">


            <button
                class="nav-btn next"
                type="button"
                aria-label="Imagen siguiente">

                <i
                    class="ri-arrow-right-s-line"
                    aria-hidden="true">
                </i>

            </button>

        </div>


        <div
            class="thumbs-wrapper mt-3"
            role="tablist"
            aria-label="Property thumbnails">

            <?php if (!empty($propertyImages)): ?>

                <?php foreach (
                    $propertyImages
                    as $index => $image
                ): ?>

                    <?php

                    $imageUrl =
                        "uploads/properties/" .
                        $image["image"];

                    ?>

                    <button
                        class="thumb <?= $index === 0
                                            ? "active"
                                            : ""; ?>"
                        type="button"
                        data-img="<?= htmlspecialchars($imageUrl); ?>"
                        aria-label="Ver imagen <?= $index + 1; ?>"
                        role="tab"
                        aria-selected="<?= $index === 0
                                            ? "true"
                                            : "false"; ?>">

                        <img
                            src="<?= htmlspecialchars($imageUrl); ?>"
                            alt="<?= htmlspecialchars(
                                        $property["name"]
                                    ); ?> - Image <?= $index + 1; ?>"
                            loading="lazy"
                            decoding="async">

                    </button>

                <?php endforeach; ?>

            <?php else: ?>

                <button
                    class="thumb active"
                    type="button"
                    data-img="img/cardHome.jpg"
                    aria-label="Ver imagen 1"
                    role="tab"
                    aria-selected="true">

                    <img
                        src="img/cardHome.jpg"
                        alt="<?= htmlspecialchars(
                                    $property["name"]
                                ); ?>"
                        loading="lazy"
                        decoding="async">

                </button>

            <?php endif; ?>

        </div>

    </section>

    <section class="property-detail container py-5">
        <div class="row g-4">
            <div class="col-lg-8">

                <div class="section-block">

                    <div class="d-flex mb-4 align-items-center">

                        <img
                            src="img/iconT.webp"
                            alt=""
                            class="iconTitles img-fluid"
                            loading="lazy"
                            decoding="async">

                        <h2 class="ms-2 mt-3 section-title">
                            About this property
                        </h2>

                    </div>


                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $property["description"]
                            )
                        ); ?>

                    </p>

                </div>

                <div class="section-block mt-5">
                    <div class="d-flex mb-4 align-items-center">
                        <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" loading="lazy" decoding="async">
                        <h2 class="ms-2 mt-3 section-title">Property Overview</h2>
                    </div>

                    <div class="overview-grid">


                        <!-- PROPERTY TYPE -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-file-list-3-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Property Type
                                </span>

                                <strong>
                                    <?= htmlspecialchars(
                                        $propertyType
                                    ); ?>
                                </strong>

                            </div>

                        </div>


                        <!-- BEDROOMS -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-hotel-bed-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Bedrooms
                                </span>

                                <strong>
                                    <?= (int) $property["bedrooms"]; ?>
                                </strong>

                            </div>

                        </div>


                        <!-- PARKING -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-car-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Parking
                                </span>

                                <strong>

                                    <?= in_array(
                                        "parking",
                                        $amenities,
                                        true
                                    )
                                        ? "Yes"
                                        : "No"; ?>

                                </strong>

                            </div>

                        </div>


                        <!-- BATHROOMS -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-drop-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Bathrooms
                                </span>

                                <strong>

                                    <?= (int) $property["bathrooms"]; ?>

                                </strong>

                            </div>

                        </div>


                        <!-- ELEVATOR -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-arrow-up-down-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Elevator
                                </span>

                                <strong>

                                    <?= in_array(
                                        "elevator",
                                        $amenities,
                                        true
                                    )
                                        ? "Yes"
                                        : "No"; ?>

                                </strong>

                            </div>

                        </div>


                        <!-- WIFI -->

                        <div class="overview-item">

                            <div class="overview-icon">

                                <i
                                    class="ri-wifi-line"
                                    aria-hidden="true">
                                </i>

                            </div>

                            <div class="overview-text">

                                <span>
                                    Wifi
                                </span>

                                <strong>

                                    <?= in_array(
                                        "wifi",
                                        $amenities,
                                        true
                                    )
                                        ? "Yes"
                                        : "No"; ?>

                                </strong>

                            </div>

                        </div>

                    </div>
                </div>

                <div class="section-block mt-5">
                    <div class="d-flex mb-4 align-items-center">
                        <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" loading="lazy" decoding="async">
                        <h2 class="ms-2 mt-3 section-title">Features & amenities</h2>
                    </div>

                    <div class="row features-list">

                        <?php if (!empty($tags)): ?>

                            <?php foreach ($tags as $tag): ?>

                                <div class="col-md-4">

                                    <ul>

                                        <li>

                                            <i
                                                class="ri-checkbox-line"
                                                aria-hidden="true">
                                            </i>

                                            <?= htmlspecialchars(
                                                $tag
                                            ); ?>

                                        </li>

                                    </ul>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="col-12">

                                <p>
                                    No additional features registered.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

                <div class="section-block mt-5">
                    <div class="d-flex mb-4 align-items-center">
                        <img src="img/iconT.webp" alt="" class="iconTitles img-fluid" loading="lazy" decoding="async">
                        <h2 class="ms-2 mt-3 section-title">Location</h2>
                    </div>

                    <div class="ratio ratio-16x9">

                        <iframe
                            title="Property location map"

                            src="https://www.google.com/maps?q=<?= urlencode(
                                                                    $property["latitude"] . "," .
                                                                        $property["longitude"]
                                                                ); ?>&output=embed"

                            width="600"
                            height="450"

                            style="border:0;"

                            allowfullscreen

                            loading="lazy"

                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-wrapper">
                    <div class="contact-card mb-4">
                        <h2 class="h5">Contact the listing owner</h2>


                        <form
                            id="propertyContactForm"
                            method="POST"
                            novalidate>

                            <!-- PROPERTY ID -->

                            <input
                                type="hidden"
                                name="property_id"
                                value="<?= (int) $property["id"]; ?>">


                            <!-- TIME TOKEN -->

                            <input
                                type="hidden"
                                name="form_time"
                                id="formTime"
                                value="">


                            <!-- HONEYPOT ANTI-BOT -->

                            <div
                                class="contact-hp"
                                aria-hidden="true">

                                <label for="website">
                                    Website
                                </label>

                                <input
                                    type="text"
                                    id="website"
                                    name="website"
                                    tabindex="-1"
                                    autocomplete="off">

                            </div>


                            <!-- NAME -->

                            <div class="input-group mb-3">

                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    id="contactName"
                                    placeholder="Your name"
                                    aria-label="Your name"
                                    maxlength="100"
                                    autocomplete="name"
                                    required>

                                <span class="input-group-text">

                                    <i
                                        class="ri-user-line"
                                        aria-hidden="true">
                                    </i>

                                </span>

                            </div>


                            <!-- EMAIL -->

                            <div class="input-group mb-3">

                                <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    id="contactEmail"
                                    placeholder="Your email"
                                    aria-label="Your email"
                                    maxlength="150"
                                    autocomplete="email"
                                    required>

                                <span class="input-group-text">

                                    <i
                                        class="ri-mail-line"
                                        aria-hidden="true">
                                    </i>

                                </span>

                            </div>


                            <!-- MESSAGE -->

                            <div class="input-group mb-3">

                                <textarea
                                    class="form-control"
                                    name="message"
                                    id="contactMessage"
                                    rows="4"
                                    placeholder="Your message"
                                    aria-label="Your message"
                                    maxlength="2000"
                                    required></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-send w-100">

                                <i
                                    class="ri-send-plane-line me-1"
                                    aria-hidden="true">
                                </i>

                                Send

                            </button>

                        </form>



                    </div>

                    <div class="author-card">

                        <h2 class="h6">
                            Author Info
                        </h2>


                        <div class="author-header">

                            <img
                                src="<?= htmlspecialchars(
                                            $authorPhoto
                                        ); ?>"
                                alt="Profile photo of <?= htmlspecialchars(
                                                            $property["author_name"]
                                                        ); ?>"
                                loading="lazy"
                                decoding="async">


                            <div>

                                <strong>

                                    <?= htmlspecialchars(
                                        $property["author_name"]
                                    ); ?>

                                </strong>


                                <p class="ubication">

                                    <i
                                        class="ri-map-pin-line"
                                        aria-hidden="true">
                                    </i>

                                    <?= htmlspecialchars(
                                        $property["canton"]
                                    ); ?>,

                                    <?= htmlspecialchars(
                                        $property["province"]
                                    ); ?>

                                </p>

                            </div>

                        </div>


                        <ul class="author-info">

                            <li>

                                <i
                                    class="ri-smartphone-line"
                                    aria-hidden="true">
                                </i>

                                <a
                                    href="tel:+50687061799">

                                    (+506) 8706-1799

                                </a>

                            </li>


                            <li>

                                <i
                                    class="ri-mail-line"
                                    aria-hidden="true">
                                </i>

                                <a
                                    href="mailto:<?= htmlspecialchars(
                                                        $property["author_email"]
                                                    ); ?>">

                                    <?= htmlspecialchars(
                                        $property["author_email"]
                                    ); ?>

                                </a>

                            </li>

                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="js/propertyContact.js"></script>


    <!--slider-->
    <script>
        const thumbs = document.querySelectorAll('.thumb');
        const mainImage = document.getElementById('mainImage');
        const prevBtn = document.querySelector('.prev');
        const nextBtn = document.querySelector('.next');

        let currentIndex = 0;

        function updateSlider(index) {

            if (!thumbs[index]) return;

            // active state
            thumbs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });

            thumbs[index].classList.add('active');
            thumbs[index].setAttribute('aria-selected', 'true');

            // animation start
            mainImage.classList.add('fade-out');

            setTimeout(() => {

                mainImage.src = thumbs[index].dataset.img;

                mainImage.onload = () => {
                    mainImage.classList.remove('fade-out');
                };

            }, 180);

            currentIndex = index;
        }

        // thumbs click
        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                updateSlider(index);
            });
        });

        // arrows
        prevBtn.addEventListener('click', () => {
            updateSlider((currentIndex - 1 + thumbs.length) % thumbs.length);
        });

        nextBtn.addEventListener('click', () => {
            updateSlider((currentIndex + 1) % thumbs.length);
        });

        // keyboard support
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') nextBtn.click();
            if (e.key === 'ArrowLeft') prevBtn.click();
        });
    </script>

</body>





</html>