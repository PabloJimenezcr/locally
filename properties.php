<?php

require_once "includes/conexion.php";


/* =========================================================
   FILTROS RECIBIDOS
========================================================= */

$province = trim($_GET["province"] ?? "");

$maxPrice = filter_input(
    INPUT_GET,
    "max_price",
    FILTER_VALIDATE_FLOAT
);

$propertyType = trim($_GET["property_type"] ?? "");

$bedrooms = filter_input(
    INPUT_GET,
    "bedrooms",
    FILTER_VALIDATE_INT
);

$bathrooms = filter_input(
    INPUT_GET,
    "bathrooms",
    FILTER_VALIDATE_INT
);

$search = trim($_GET["search"] ?? "");

$sort = $_GET["sort"] ?? "newest";


/* =========================================================
   PAGINACIÓN
========================================================= */

$page = filter_input(
    INPUT_GET,
    "page",
    FILTER_VALIDATE_INT
);

$page = ($page && $page > 0)
    ? $page
    : 1;

$perPage = 12;

$offset = ($page - 1) * $perPage;


/* =========================================================
   CONDICIONES SQL
========================================================= */

$where = [
    "p.status = 'active'"
];

$params = [];

$types = "";


/* =========================================================
   BÚSQUEDA
========================================================= */

if ($search !== "") {

    $where[] = "(
        p.name LIKE ?
        OR p.province LIKE ?
        OR p.canton LIKE ?
        OR p.address LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ssss";
}


/* =========================================================
   PROVINCIA
========================================================= */

if ($province !== "") {

    $where[] = "p.province = ?";

    $params[] = $province;

    $types .= "s";
}


/* =========================================================
   PRECIO MÁXIMO
========================================================= */

if (
    $maxPrice !== false &&
    $maxPrice !== null &&
    $maxPrice > 0
) {

    $where[] = "p.price <= ?";

    $params[] = $maxPrice;

    $types .= "d";
}


/* =========================================================
   TIPO DE PROPIEDAD
========================================================= */

if ($propertyType !== "") {

    $where[] = "p.property_type = ?";

    $params[] = $propertyType;

    $types .= "s";
}


/* =========================================================
   HABITACIONES
========================================================= */

if (
    $bedrooms !== false &&
    $bedrooms !== null &&
    $bedrooms > 0
) {

    $where[] = "p.bedrooms >= ?";

    $params[] = $bedrooms;

    $types .= "i";
}


/* =========================================================
   BAÑOS
========================================================= */

if (
    $bathrooms !== false &&
    $bathrooms !== null &&
    $bathrooms > 0
) {

    $where[] = "p.bathrooms >= ?";

    $params[] = $bathrooms;

    $types .= "i";
}


$whereSql = implode(" AND ", $where);


/* =========================================================
   ORDENAMIENTO
========================================================= */

$orderSql = "p.created_at DESC";

switch ($sort) {

    case "oldest":
        $orderSql = "p.created_at ASC";
        break;

    case "price_asc":
        $orderSql = "p.price ASC";
        break;

    case "price_desc":
        $orderSql = "p.price DESC";
        break;

    case "newest":
    default:
        $orderSql = "p.created_at DESC";
        $sort = "newest";
        break;
}


/* =========================================================
   CONTAR PROPIEDADES
========================================================= */

$countSql = "
    SELECT COUNT(*) AS total
    FROM properties p
    WHERE $whereSql
";

$countStmt = $conn->prepare($countSql);

if (!empty($params)) {

    $countStmt->bind_param(
        $types,
        ...$params
    );
}

$countStmt->execute();

$countResult = $countStmt->get_result();

$totalProperties = (int) (
    $countResult->fetch_assoc()["total"] ?? 0
);

$countStmt->close();


/* =========================================================
   TOTAL DE PÁGINAS
========================================================= */

$totalPages = max(
    1,
    (int) ceil(
        $totalProperties / $perPage
    )
);


/*
 * Evitar páginas inexistentes.
 */

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}


/* =========================================================
   OBTENER PROPIEDADES
========================================================= */

$sql = "
    SELECT
        p.id,
        p.name,
        p.province,
        p.canton,
        p.address,
        p.price,
        p.size,
        p.size_unit,
        p.property_type,
        p.bedrooms,
        p.bathrooms,
        p.created_at,

        u.name AS user_name,
        u.photo AS user_photo,

        pi.image

    FROM properties p

    INNER JOIN users u
        ON u.id = p.user_id

    LEFT JOIN property_images pi
        ON pi.property_id = p.id
        AND pi.id = (
            SELECT MIN(pi2.id)
            FROM property_images pi2
            WHERE pi2.property_id = p.id
        )

    WHERE $whereSql

    ORDER BY $orderSql

    LIMIT ? OFFSET ?
";


$propertyParams = $params;

$propertyTypes = $types;

$propertyParams[] = $perPage;
$propertyParams[] = $offset;

$propertyTypes .= "ii";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    $propertyTypes,
    ...$propertyParams
);

$stmt->execute();

$result = $stmt->get_result();

$properties = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();


/* =========================================================
   VALORES SELECCIONADOS
========================================================= */

$selectedBedrooms = (
    $bedrooms !== false &&
    $bedrooms !== null &&
    $bedrooms > 0
)
    ? $bedrooms
    : 0;


$selectedBathrooms = (
    $bathrooms !== false &&
    $bathrooms !== null &&
    $bathrooms > 0
)
    ? $bathrooms
    : 0;


/* =========================================================
   PROPIEDADES RECIENTES PARA FOOTER
========================================================= */

$recentStmt = $conn->prepare("
    SELECT
        p.id,
        p.canton,
        p.province,
        p.created_at,
        pi.image

    FROM properties p

    LEFT JOIN property_images pi
        ON pi.property_id = p.id
        AND pi.id = (
            SELECT MIN(pi2.id)
            FROM property_images pi2
            WHERE pi2.property_id = p.id
        )

    WHERE p.status = 'active'

    ORDER BY p.created_at DESC

    LIMIT 2
");

$recentStmt->execute();

$recentResult = $recentStmt->get_result();

$recentProperties = $recentResult->fetch_all(
    MYSQLI_ASSOC
);

$recentStmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Properties | Locally</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>


    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">


    <link
        rel="stylesheet"
        href="css/properties.css">


    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
        rel="stylesheet">

</head>


<body>


    <!-- ================= HEADER ================= -->

    <header class="hProperty">

        <nav class="navbar navbar-expand-lg">
            <div class="container">

                <ul class="navbar-nav d-none d-lg-flex me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                </ul>

                <a href="index.php">
                    <img src="img/logoLocally.png" class="img-fluid" alt="Locally logo">
                </a>

                <ul class="navbar-nav d-none d-lg-flex ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="properties.php">Properties</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#projects">Portfolio</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#blog">Blog</a>
                    </li>

                </ul>
                <button class="btnMobileMenu d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-label="Open menu">

                    <i class="ri-menu-3-line" aria-hidden="true"></i>

                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
                    <div class="offcanvas-header">
                        <img src="img/logoLocally.png" class="img-fluid logoMobile" alt="Locally logo">
                        <button type="button" class="customCloseBtn" data-bs-dismiss="offcanvas"
                            aria-label="Close menu">

                            <i class="ri-close-line"></i>

                        </button>
                    </div>

                    <div class="offcanvas-body">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#about">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#services">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#blog">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#projects">Portfolio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="properties.php">Properties</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </nav>

    </header>


    <!-- ================= HERO ================= -->

    <section class="hero d-flex align-items-center text-center">

        <div class="container">

            <h1 class="hero-title">

                Discover Your
                <span>Masterpiece.</span>

            </h1>


            <p class="hero-sub">

                Curated architectural wonders for the discerning collector.

            </p>


            <!-- SEARCH BAR -->

            <form
                class="hero-search"
                role="search"
                method="GET"
                action="properties.php">


                <div class="search-group">

                    <i
                        class="ri-map-pin-2-line"
                        aria-hidden="true">
                    </i>


                    <input
                        type="text"
                        name="search"
                        value="<?= htmlspecialchars($search); ?>"
                        placeholder="Where do you want to live?"
                        aria-label="Search location"
                        autocomplete="off">

                </div>


                <div
                    class="divider"
                    aria-hidden="true">
                </div>


                <div class="search-group">

                    <i
                        class="ri-building-line"
                        aria-hidden="true">
                    </i>


                    <select
                        name="property_type"
                        aria-label="Property type">

                        <option value="">
                            Any Property Type
                        </option>

                        <option
                            value="house"
                            <?= $propertyType === "house"
                                ? "selected"
                                : ""; ?>>
                            House
                        </option>

                        <option
                            value="lot"
                            <?= $propertyType === "lot"
                                ? "selected"
                                : ""; ?>>
                            Lot
                        </option>

                        <option
                            value="apartment"
                            <?= $propertyType === "apartment"
                                ? "selected"
                                : ""; ?>>
                            Apartment
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary-custom">

                    <i
                        class="ri-search-line"
                        aria-hidden="true">
                    </i>

                    Find Estates

                </button>

            </form>

        </div>

    </section>


    <!-- ================= MAIN ================= -->

    <div class="container-fluid px-5 mt-5">

        <div class="row">


            <!-- ================= SIDEBAR ================= -->

            <aside class="col-lg-3">


                <form
                    method="GET"
                    action="properties.php"
                    id="filtersForm">


                    <div class="filters">


                        <h2>
                            Filters
                        </h2>

                        <p class="small">
                            Refine your search
                        </p>


                        <!-- ================= PROVINCE ================= -->

                        <div class="filter-block active">

                            <i
                                class="ri-map-line"
                                aria-hidden="true">
                            </i>

                            <span>
                                Province
                            </span>

                        </div>


                        <select
                            class="form-select custom-select"
                            name="province"
                            aria-label="Province">

                            <option value="">
                                All Provinces
                            </option>


                            <option
                                value="San José"
                                <?= $province === "San José"
                                    ? "selected"
                                    : ""; ?>>
                                San José
                            </option>


                            <option
                                value="Alajuela"
                                <?= $province === "Alajuela"
                                    ? "selected"
                                    : ""; ?>>
                                Alajuela
                            </option>


                            <option
                                value="Cartago"
                                <?= $province === "Cartago"
                                    ? "selected"
                                    : ""; ?>>
                                Cartago
                            </option>


                            <option
                                value="Heredia"
                                <?= $province === "Heredia"
                                    ? "selected"
                                    : ""; ?>>
                                Heredia
                            </option>


                            <option
                                value="Guanacaste"
                                <?= $province === "Guanacaste"
                                    ? "selected"
                                    : ""; ?>>
                                Guanacaste
                            </option>


                            <option
                                value="Puntarenas"
                                <?= $province === "Puntarenas"
                                    ? "selected"
                                    : ""; ?>>
                                Puntarenas
                            </option>


                            <option
                                value="Limón"
                                <?= $province === "Limón"
                                    ? "selected"
                                    : ""; ?>>
                                Limón
                            </option>

                        </select>


                        <!-- ================= PRICE ================= -->

                        <div class="filter-block">
                            <i class="ri-money-dollar-box-line" aria-hidden="true"></i>
                            <span>
                                Maximum Price
                            </span>
                        </div>


                        <div class="price-range">

                            <input
                                type="range"
                                name="max_price"
                                min="500000"
                                max="10000000"
                                step="100000"
                                value="<?= $maxPrice
                                            ? (float) $maxPrice
                                            : 10000000; ?>"
                                id="priceRange"
                                aria-label="Maximum price">


                            <div class="price-labels d-flex justify-content-between">

                                <span id="maxPriceLabel">
                                    $10M+
                                </span>

                                <span>
                                    $10M+
                                </span>

                            </div>



                        </div>


                        <!-- ================= PROPERTY TYPE ================= -->

                        <div class="filter-block">

                            <i
                                class="ri-building-2-line"
                                aria-hidden="true">
                            </i>

                            <span>
                                Property Type
                            </span>

                        </div>


                        <div class="d-flex gap-2 flex-wrap">

                            <button
                                type="button"
                                class="chip <?= $propertyType === "" ? "active" : ""; ?>"
                                data-property-type="">
                                All
                            </button>

                            <button
                                type="button"
                                class="chip <?= $propertyType === "house" ? "active" : ""; ?>"
                                data-property-type="house">
                                House
                            </button>

                            <button
                                type="button"
                                class="chip <?= $propertyType === "lot" ? "active" : ""; ?>"
                                data-property-type="lot">
                                Lot
                            </button>

                            <button
                                type="button"
                                class="chip <?= $propertyType === "apartment" ? "active" : ""; ?>"
                                data-property-type="apartment">
                                Apartment
                            </button>

                        </div>


                        <input
                            type="hidden"
                            name="property_type"
                            id="propertyTypeInput"
                            value="<?= htmlspecialchars($propertyType); ?>">


                        <!-- ================= BEDROOMS ================= -->

                        <div class="filter-block">

                            <i
                                class="ri-hotel-bed-line"
                                aria-hidden="true">
                            </i>

                            <span>
                                Minimum Bedrooms
                            </span>

                        </div>


                        <div class="grid-buttons">

                            <button
                                type="button"
                                class="<?= $selectedBedrooms === 0 ? "active" : ""; ?>"
                                data-bedrooms="0">

                                Any

                            </button>


                            <?php for ($i = 1; $i <= 4; $i++): ?>

                                <button
                                    type="button"
                                    class="<?= $selectedBedrooms === $i ? "active" : ""; ?>"
                                    data-bedrooms="<?= $i; ?>">

                                    <?= $i; ?>

                                </button>

                            <?php endfor; ?>


                            <button
                                type="button"
                                class="<?= $selectedBedrooms >= 5 ? "active" : ""; ?>"
                                data-bedrooms="5">

                                5+

                            </button>

                        </div>





                        <input
                            type="hidden"
                            name="bedrooms"
                            id="bedroomsInput"
                            value="<?= $selectedBedrooms; ?>">


                        <!-- ================= BATHROOMS ================= -->

                        <div class="filter-block">

                            <i
                                class="ri-shower-line"
                                aria-hidden="true">
                            </i>

                            <span>
                                Minimum Bathrooms
                            </span>

                        </div>


                        <div class="grid-buttons">

                            <button
                                type="button"
                                class="<?= $selectedBathrooms === 0 ? "active" : ""; ?>"
                                data-bathrooms="0">

                                Any

                            </button>


                            <?php for ($i = 1; $i <= 4; $i++): ?>

                                <button
                                    type="button"
                                    class="<?= $selectedBathrooms === $i ? "active" : ""; ?>"
                                    data-bathrooms="<?= $i; ?>">

                                    <?= $i; ?>

                                </button>

                            <?php endfor; ?>


                            <button
                                type="button"
                                class="<?= $selectedBathrooms >= 5 ? "active" : ""; ?>"
                                data-bathrooms="5">

                                5+

                            </button>

                        </div>


                        <input
                            type="hidden"
                            name="bathrooms"
                            id="bathroomsInput"
                            value="<?= $selectedBathrooms; ?>">


                        <!-- ================= APPLY ================= -->

                        <button
                            type="submit"
                            class="btn btn-primary-custom w-100 mt-4">

                            Apply Filters

                        </button>


                        <?php if (
                            $province !== "" ||
                            ($maxPrice !== false && $maxPrice !== null && $maxPrice < 10000000) ||
                            $propertyType !== "" ||
                            $selectedBedrooms > 0 ||
                            $selectedBathrooms > 0 ||
                            $search !== ""
                        ): ?>

                            <a
                                href="properties.php"
                                class="clear-filters">

                                Clear Filters

                            </a>

                        <?php endif; ?>


                    </div>

                </form>

            </aside>


            <!-- ================= LISTINGS ================= -->

            <section class="col-lg-9">


                <div
                    class="d-flex justify-content-between align-items-center mb-4">


                    <div>

                        <h2>
                            Available Properties
                        </h2>


                        <p class="small">

                            Displaying

                            <?= $totalProperties; ?>

                            <?= $totalProperties === 1
                                ? "property"
                                : "properties"; ?>

                        </p>

                    </div>


                    <select
                        class="btn sort-btn"
                        name="sort"
                        id="sortSelect"
                        form="filtersForm">

                        <option
                            value="newest"
                            <?= $sort === "newest"
                                ? "selected"
                                : ""; ?>>
                            Newest First
                        </option>


                        <option
                            value="oldest"
                            <?= $sort === "oldest"
                                ? "selected"
                                : ""; ?>>
                            Oldest First
                        </option>


                        <option
                            value="price_asc"
                            <?= $sort === "price_asc"
                                ? "selected"
                                : ""; ?>>
                            Price: Low to High
                        </option>


                        <option
                            value="price_desc"
                            <?= $sort === "price_desc"
                                ? "selected"
                                : ""; ?>>
                            Price: High to Low
                        </option>

                    </select>

                </div>


                <!-- ================= CARDS ================= -->

                <div class="row g-4">


                    <?php if (!empty($properties)): ?>


                        <?php foreach ($properties as $property): ?>


                            <?php

                            $propertyImage = !empty($property["image"])
                                ? "uploads/properties/"
                                . $property["image"]
                                : "img/cardHome.jpg";


                            $userPhoto = !empty($property["user_photo"])
                                ? "uploads/profiles/"
                                . $property["user_photo"]
                                : "img/mari.jpg";

                            ?>


                            <div
                                class="col-xl-6 col-lg-6 col-md-6">


                                <a
                                    href="propertyUser.php?id=<?= (int) $property["id"]; ?>"
                                    class="propertyCard"
                                    aria-label="View property <?= htmlspecialchars($property["name"]); ?>">


                                    <div
                                        class="propertyImg d-flex justify-content-center align-items-center">


                                        <img
                                            src="<?= htmlspecialchars($propertyImage); ?>"
                                            loading="lazy"
                                            decoding="async"
                                            alt="<?= htmlspecialchars($property["name"]); ?>">


                                        <div class="badges">

                                            <p class="sale">
                                                For Sale
                                            </p>

                                        </div>


                                        <div class="location">

                                            <i
                                                class="ri-map-pin-2-line"
                                                aria-hidden="true">
                                            </i>

                                            <?= htmlspecialchars($property["canton"]); ?>,
                                            <?= htmlspecialchars($property["province"]); ?>

                                        </div>

                                    </div>


                                    <div class="bodyCard">


                                        <h3>
                                            <?= htmlspecialchars($property["name"]); ?>
                                        </h3>


                                        <div class="propertyFeatures">


                                            <p>

                                                <i
                                                    class="ri-hotel-bed-line"
                                                    aria-hidden="true">
                                                </i>

                                                <?= (int) $property["bedrooms"]; ?>

                                                Beds

                                            </p>


                                            <p>

                                                <i
                                                    class="uil uil-bath"
                                                    aria-hidden="true">
                                                </i>

                                                <?= (int) $property["bathrooms"]; ?>

                                                Baths

                                            </p>


                                            <p>

                                                <i
                                                    class="ri-ruler-line"
                                                    aria-hidden="true">
                                                </i>

                                                <?= htmlspecialchars($property["size"]); ?>

                                                <?= htmlspecialchars($property["size_unit"]); ?>

                                            </p>


                                        </div>


                                        <div class="propertyFooter">


                                            <div class="agent">


                                                <img
                                                    src="<?= htmlspecialchars($userPhoto); ?>"
                                                    loading="lazy"
                                                    decoding="async"
                                                    alt="<?= htmlspecialchars($property["user_name"]); ?>">


                                                <span>

                                                    <?= htmlspecialchars($property["user_name"]); ?>

                                                </span>


                                            </div>


                                            <span class="price">

                                                $<?= number_format(
                                                        (float) $property["price"],
                                                        0,
                                                        ".",
                                                        ","
                                                    ); ?>

                                            </span>


                                        </div>


                                    </div>


                                </a>

                            </div>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <div class="col-12">

                            <div class="text-center py-5">

                                <i
                                    class="ri-search-eye-line"
                                    style="font-size: 50px;">
                                </i>


                                <h3>
                                    No properties found
                                </h3>


                                <p>
                                    Try changing your filters.
                                </p>

                            </div>

                        </div>


                    <?php endif; ?>


                </div>


                <!-- ================= PAGINATION ================= -->

                <?php if ($totalPages > 1): ?>

                    <nav
                        class="pagination-custom d-flex justify-content-center align-items-center mt-5 gap-2 mb-4"
                        aria-label="Pagination">


                        <?php if ($page > 1): ?>

                            <a
                                class="page-btn"
                                href="?<?= http_build_query(
                                            array_merge(
                                                $_GET,
                                                [
                                                    "page" => $page - 1
                                                ]
                                            )
                                        ); ?>">

                                <i
                                    class="ri-arrow-left-s-line"
                                    aria-hidden="true">
                                </i>

                            </a>

                        <?php endif; ?>


                        <?php for (
                            $i = 1;
                            $i <= $totalPages;
                            $i++
                        ): ?>


                            <a
                                class="page-btn <?= $i === $page
                                                    ? "active"
                                                    : ""; ?>"
                                href="?<?= http_build_query(
                                            array_merge(
                                                $_GET,
                                                [
                                                    "page" => $i
                                                ]
                                            )
                                        ); ?>">

                                <?= $i; ?>

                            </a>


                        <?php endfor; ?>


                        <?php if ($page < $totalPages): ?>


                            <a
                                class="page-btn"
                                href="?<?= http_build_query(
                                            array_merge(
                                                $_GET,
                                                [
                                                    "page" => $page + 1
                                                ]
                                            )
                                        ); ?>">

                                <i
                                    class="ri-arrow-right-s-line"
                                    aria-hidden="true">
                                </i>

                            </a>


                        <?php endif; ?>


                    </nav>

                <?php endif; ?>


            </section>

        </div>

    </div>



    <!-- WHATSAPP FLOAT -->
    <a
        href="https://wa.me/50661189054"
        class="whatsappFloat"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Contact Locally on WhatsApp">

        <span class="whatsappPulse"></span>

        <span class="whatsappIcon">
            <i class="ri-whatsapp-line" aria-hidden="true"></i>
        </span>

        <span class="whatsappText">
            Let's talk
        </span>

        <span class="whatsappDot"></span>

    </a>



    <!-- ================= FOOTER ================= -->

    <footer class="footer-wrapper">

        <div class="footer pt-5">
            <div class="container">
                <div class="row gy-4 mt-5">

                    <div class="col-lg-3 col-md-6">
                        <img src="img/logoLocally.png" alt="Locally Costa Rica logo" class="img-fluid mb-3">
                        <p>
                            Every project we develop is designed to reflect a brand’s identity, purpose, and sense of place, enabling brands to communicate in a more natural and memorable way.
                        </p>

                        <div class="social-icons mt-3">
                            <a href="#" aria-label="Visit our Facebook">
                                <i class="ri-facebook-fill" aria-hidden="true"></i>
                            </a>
                            <a href="#" aria-label="Visit our YouTube">
                                <i class="ri-youtube-fill" aria-hidden="true"></i>
                            </a>
                            <a href="#" aria-label="Visit our LinkedIn">
                                <i class="ri-linkedin-fill" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 pt-5">
                        <h5 class="footer-title">Quick Links</h5>
                        <ul class="footer-links">
                            <li>
                                <a href="index.php"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Home</a>
                            </li>
                            <li>
                                <a href="#about"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> About</a>
                            </li>
                            <li>
                                <a href="#services"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Services</a>
                            </li>

                            <li>
                                <a href="properties.php"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Properties</a>
                            </li>

                            <li>
                                <a href="#projects"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Portfolio</a>
                            </li>

                            <li>
                                <a href="#blog"><i class="ri-arrow-right-s-line" aria-hidden="true"></i> Blog</a>
                            </li>


                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-6 pt-5">
                        <h5 class="footer-title">Recent Properties</h5>

                        <?php if (!empty($recentProperties)): ?>

                            <?php foreach ($recentProperties as $recentProperty): ?>

                                <?php

                                $recentImage = !empty($recentProperty["image"])
                                    ? "uploads/properties/" . $recentProperty["image"]
                                    : "img/cardHome.jpg";

                                $recentDate = date(
                                    "d M, Y",
                                    strtotime($recentProperty["created_at"])
                                );

                                ?>

                                <a
                                    href="propertyUser.php?id=<?= (int) $recentProperty["id"]; ?>"
                                    class="post d-flex mb-3 text-decoration-none">



                                    <img
                                        src="<?= htmlspecialchars($recentImage); ?>"
                                        alt="Property in <?= htmlspecialchars($recentProperty["canton"]); ?>, <?= htmlspecialchars($recentProperty["province"]); ?>">

                                    <div class="ms-3">

                                        <small>
                                            <i
                                                class="ri-calendar-line"
                                                aria-hidden="true">
                                            </i>

                                            <?= htmlspecialchars($recentDate); ?>
                                        </small>

                                        <p>
                                            <?= htmlspecialchars($recentProperty["canton"]); ?>,
                                            <?= htmlspecialchars($recentProperty["province"]); ?>
                                        </p>

                                    </div>

                                </a>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p>No properties available.</p>

                        <?php endif; ?>

                    </div>

                    <div class="col-lg-2 col-md-6 pt-5 contactInfo">
                        <h5 class="footer-title">Contact Us</h5>

                        <p>
                            <i class="ri-mail-line" aria-hidden="true"></i>
                            <a href="mailto:sales@locally.cr">sales@locally.cr</a>
                        </p>

                        <p>
                            <i class="ri-phone-line" aria-hidden="true"></i>
                            <a href="tel:+50661189054">(+506) 6118-9054</a>
                        </p>
                    </div>

                </div>
            </div>

            <div class="footer-bottom mt-5">
                <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p>© All Copyright 2026 by Locally Costa Rica</p>

                    <div>
                        <a href="#">Terms & Conditions</a>
                        <a href="#">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>

    </footer>


    <!-- ================= BOOTSTRAP ================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- ================= PROPERTIES JS ================= -->

    <script src="js/properties.js"></script>


</body>

</html>