<?php

session_start();

if (!isset($_SESSION["id"])) {
  header("Location: login.php");
  exit;
}

require_once "includes/conexion.php";

$userId = (int) $_SESSION["id"];


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
    Solo el propietario puede editarla
==================================================*/

$stmt = $conn->prepare("
    SELECT
        id,
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


/*==================================================
    PROPIEDAD NO ENCONTRADA
==================================================*/

if (!$property) {

  header("Location: dashboard.php");
  exit;
}


/*==================================================
    DECODIFICAR AMENITIES
==================================================*/

$amenities = [];

if (!empty($property["amenities"])) {

  $decodedAmenities =
    json_decode(
      $property["amenities"],
      true
    );

  if (is_array($decodedAmenities)) {

    $amenities = $decodedAmenities;
  }
}


/*==================================================
    DECODIFICAR TAGS
==================================================*/

$tags = [];

if (!empty($property["tags"])) {

  $decodedTags =
    json_decode(
      $property["tags"],
      true
    );

  if (is_array($decodedTags)) {

    $tags = $decodedTags;
  }
}


/*==================================================
    OBTENER IMÁGENES
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
  $imageResult->fetch_all(MYSQLI_ASSOC);

$imageStmt->close();


/*==================================================
    DATOS PARA JAVASCRIPT
==================================================*/

$propertyJson = json_encode(
  [
    "id" => (int) $property["id"],
    "name" => $property["name"],
    "province" => $property["province"],
    "canton" => $property["canton"],
    "price" => $property["price"],
    "size" => $property["size"],
    "size_unit" => $property["size_unit"],
    "property_type" => $property["property_type"],
    "bedrooms" => (int) $property["bedrooms"],
    "bathrooms" => (int) $property["bathrooms"],
    "amenities" => $amenities,
    "tags" => $tags,
    "description" => $property["description"],
    "status" => $property["status"],
    "address" => $property["address"],
    "latitude" => $property["latitude"],
    "longitude" => $property["longitude"]
  ],
  JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="css/addProperty.css">
  <link rel="stylesheet" href="css/editProperty.css">
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


  <header class="container-fluid px-5 py-4">

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



  <div class="container-fluid px-5 py-4 form-container ">

    <form
      id="propertyForm"
      action="actions/updateProperty.php"
      method="POST"
      enctype="multipart/form-data">

      <!-- ID DE LA PROPIEDAD -->

      <input
        type="hidden"
        name="property_id"
        value="<?= (int) $property["id"]; ?>">


      <div class="row g-4">

        <!-- =========================================
             COLUMNA PRINCIPAL
        ========================================== -->

        <div class="col-lg-8">


          <!-- =====================================
                 01 - PRIMARY IDENTITY
            ====================================== -->

          <div class="custom-card mb-4">

            <div class="card-header-custom d-flex align-items-center gap-2">

              <span class="step">
                01
              </span>

              <h2 class="mb-0">
                Primary Identity
              </h2>

            </div>


            <!-- PROPERTY NAME -->

            <div class="mt-3">

              <label
                for="propertyName"
                class="label">

                Property Name

              </label>

              <input
                type="text"
                id="propertyName"
                name="name"
                class="form-control custom-input"
                maxlength="150"
                value="<?= htmlspecialchars(
                          $property["name"]
                        ); ?>"
                required>

            </div>


            <!-- PROVINCE / PRICE / SIZE -->

            <div class="row mt-3 g-3">


              <!-- PROVINCE -->

              <div class="col-md-4">

                <label
                  for="province"
                  class="label">

                  Province

                </label>

                <select
                  id="province"
                  name="province"
                  class="form-select custom-input"
                  required>

                  <option
                    value=""
                    disabled>

                    Select province

                  </option>


                  <option
                    value="San José"
                    <?= $property["province"] === "San José"
                      ? "selected"
                      : ""; ?>>

                    San José

                  </option>


                  <option
                    value="Alajuela"
                    <?= $property["province"] === "Alajuela"
                      ? "selected"
                      : ""; ?>>

                    Alajuela

                  </option>


                  <option
                    value="Cartago"
                    <?= $property["province"] === "Cartago"
                      ? "selected"
                      : ""; ?>>

                    Cartago

                  </option>


                  <option
                    value="Heredia"
                    <?= $property["province"] === "Heredia"
                      ? "selected"
                      : ""; ?>>

                    Heredia

                  </option>


                  <option
                    value="Guanacaste"
                    <?= $property["province"] === "Guanacaste"
                      ? "selected"
                      : ""; ?>>

                    Guanacaste

                  </option>


                  <option
                    value="Puntarenas"
                    <?= $property["province"] === "Puntarenas"
                      ? "selected"
                      : ""; ?>>

                    Puntarenas

                  </option>


                  <option
                    value="Limón"
                    <?= $property["province"] === "Limón"
                      ? "selected"
                      : ""; ?>>

                    Limón

                  </option>

                </select>

              </div>


              <!-- PRICE -->

              <div class="col-md-4">

                <label
                  for="propertyPrice"
                  class="label">

                  Price ($)

                </label>

                <input
                  type="number"
                  id="propertyPrice"
                  name="price"
                  class="form-control custom-input"
                  min="0"
                  step="0.01"
                  value="<?= htmlspecialchars(
                            $property["price"]
                          ); ?>"
                  required>

              </div>


              <!-- SIZE -->

              <div class="col-md-4">

                <label
                  for="propertySize"
                  class="label">

                  Size

                </label>


                <div class="d-flex gap-2">

                  <input
                    type="number"
                    id="propertySize"
                    name="size"
                    class="form-control custom-input"
                    min="0"
                    step="0.01"
                    value="<?= htmlspecialchars(
                              $property["size"]
                            ); ?>"
                    required>


                  <select
                    id="sizeUnit"
                    name="size_unit"
                    class="form-select custom-input">

                    <option
                      value="m2"
                      <?= $property["size_unit"] === "m2"
                        ? "selected"
                        : ""; ?>>

                      M²

                    </option>


                    <option
                      value="sqft"
                      <?= $property["size_unit"] === "sqft"
                        ? "selected"
                        : ""; ?>>

                      SQFT

                    </option>

                  </select>

                </div>

              </div>

            </div>

          </div>


          <!-- =====================================
                 02 - STRUCTURE & LOGISTICS
            ====================================== -->

          <div class="custom-card">

            <div class="card-header-custom d-flex align-items-center gap-2">

              <span class="step">
                02
              </span>

              <h2 class="mb-0">
                Structure & Logistics
              </h2>

            </div>


            <div class="row mt-3 g-3">


              <!-- PROPERTY TYPE -->

              <div class="col-md-4">

                <label
                  for="propertyType"
                  class="label">

                  Property Type

                </label>


                <select
                  id="propertyType"
                  name="property_type"
                  class="form-select custom-input"
                  required>

                  <option
                    value=""
                    disabled>

                    Select type

                  </option>


                  <option
                    value="house"
                    <?= $property["property_type"] === "house"
                      ? "selected"
                      : ""; ?>>

                    House

                  </option>


                  <option
                    value="lot"
                    <?= $property["property_type"] === "lot"
                      ? "selected"
                      : ""; ?>>

                    Lot

                  </option>


                  <option
                    value="apartment"
                    <?= $property["property_type"] === "apartment"
                      ? "selected"
                      : ""; ?>>

                    Apartment

                  </option>

                </select>

              </div>


              <!-- BEDROOMS -->

              <div class="col-md-4">

                <label class="label">
                  Bedrooms
                </label>


                <div class="counter">

                  <button
                    type="button"
                    class="btn-minus"
                    aria-label="Decrease bedrooms">

                    −

                  </button>


                  <span
                    id="bedroomsValue"
                    aria-live="polite">

                    <?= (int) $property["bedrooms"]; ?>

                  </span>


                  <button
                    type="button"
                    class="btn-plus"
                    aria-label="Increase bedrooms">

                    +

                  </button>

                </div>


                <input
                  type="hidden"
                  id="bedrooms"
                  name="bedrooms"
                  value="<?= (int) $property["bedrooms"]; ?>">

              </div>


              <!-- BATHROOMS -->

              <div class="col-md-4">

                <label class="label">
                  Bathrooms
                </label>


                <div class="counter">

                  <button
                    type="button"
                    class="btn-minus"
                    aria-label="Decrease bathrooms">

                    −

                  </button>


                  <span
                    id="bathroomsValue"
                    aria-live="polite">

                    <?= (int) $property["bathrooms"]; ?>

                  </span>


                  <button
                    type="button"
                    class="btn-plus"
                    aria-label="Increase bathrooms">

                    +

                  </button>

                </div>


                <input
                  type="hidden"
                  id="bathrooms"
                  name="bathrooms"
                  value="<?= (int) $property["bathrooms"]; ?>">

              </div>

            </div>

          </div>


          <!-- =====================================
                 03 - FEATURES & NARRATIVE
            ====================================== -->

          <div class="custom-card mt-4">

            <div class="card-header-custom d-flex align-items-center gap-2">

              <span class="step">
                03
              </span>

              <h2 class="mb-0">
                Features & Narrative
              </h2>

            </div>


            <!-- AMENITIES -->

            <div class="mt-3">

              <p class="label mb-2">
                Core Amenities
              </p>


              <div class="amenities d-flex gap-4 mt-2">


                <!-- PARKING -->

                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="parking"
                    <?= in_array(
                      "parking",
                      $amenities,
                      true
                    )
                      ? "checked"
                      : ""; ?>>

                  <span></span>

                  Parking

                </label>


                <!-- WIFI -->

                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="wifi"
                    <?= in_array(
                      "wifi",
                      $amenities,
                      true
                    )
                      ? "checked"
                      : ""; ?>>

                  <span></span>

                  Wifi

                </label>


                <!-- ELEVATOR -->

                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="elevator"
                    <?= in_array(
                      "elevator",
                      $amenities,
                      true
                    )
                      ? "checked"
                      : ""; ?>>

                  <span></span>

                  Elevator

                </label>

              </div>

            </div>


            <!-- TAGS -->

            <div class="mt-4">

              <label
                for="tagInput"
                class="label">

                Interactive Amenities Tags

              </label>


              <div class="tag-input">

                <input
                  id="tagInput"
                  type="text"
                  aria-label="Amenity tag"
                  maxlength="50">


                <button
                  id="addTagBtn"
                  type="button"
                  aria-label="Add amenity tag">

                  <i
                    class="ri-add-line"
                    aria-hidden="true">
                  </i>

                </button>

              </div>


              <div
                class="tags mt-3"
                id="tagsContainer">

                <?php foreach ($tags as $tag): ?>

                  <span>

                    <?= htmlspecialchars($tag); ?>

                    <i
                      class="ri-close-line"
                      role="button"
                      aria-label="Remove <?= htmlspecialchars($tag); ?>">
                    </i>

                  </span>

                <?php endforeach; ?>

              </div>


              <input
                type="hidden"
                id="propertyTags"
                name="tags"
                value="<?= htmlspecialchars(
                          json_encode(
                            $tags,
                            JSON_UNESCAPED_UNICODE
                          )
                        ); ?>">

            </div>


            <!-- DESCRIPTION -->

            <div class="mt-4">

              <label
                for="propertyDescription"
                class="label">

                Property Description

              </label>


              <textarea
                id="propertyDescription"
                name="description"
                class="form-control custom-input textarea"
                maxlength="5000"
                required><?= htmlspecialchars(
                            $property["description"]
                          ); ?></textarea>

            </div>

          </div>

        </div>


        <!-- =========================================
             COLUMNA DERECHA
        ========================================== -->

        <div class="col-lg-4">

          <div class="sticky-side">


            <!-- =================================
                     PROPERTY STATUS
                ================================== -->

            <div class="custom-card mb-4">

              <div class="d-flex justify-content-between align-items-center mb-3">

                <h2 class="mb-0 h6">
                  Property Status
                </h2>

              </div>


              <div class="row mt-4 d-flex justify-content-between align-items-center text-center">


                <!-- ACTIVE -->

                <div class="col-lg-6 mb-4">

                  <button
                    type="button"
                    class="boxActive box <?= $property["status"] === "active"
                                            ? "active"
                                            : ""; ?>"
                    data-status="active"
                    aria-label="Set property as active">

                    <i
                      class="ri-eye-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Active
                    </span>

                  </button>

                </div>


                <!-- INACTIVE -->

                <div class="col-lg-6 mb-4">

                  <button
                    type="button"
                    class="boxInactive box <?= $property["status"] === "inactive"
                                              ? "active"
                                              : ""; ?>"
                    data-status="inactive"
                    aria-label="Set property as inactive">

                    <i
                      class="ri-eye-off-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Inactive
                    </span>

                  </button>

                </div>

              </div>


              <input
                type="hidden"
                id="propertyStatus"
                name="status"
                value="<?= htmlspecialchars(
                          $property["status"]
                        ); ?>">

            </div>

            <!-- =================================
     MEDIA GALLERY
================================== -->

            <div class="custom-card mb-4">

              <div class="d-flex justify-content-between align-items-center mb-3">

                <h2 class="mb-0 h6">
                  Media Gallery
                </h2>

              </div>


              <p class="label mb-3">
                Upload up to 4 images
              </p>


              <div class="gallery edit-gallery">

                <?php for ($i = 0; $i < 4; $i++): ?>

                  <?php

                  $existingImage =
                    $propertyImages[$i] ?? null;

                  ?>


                  <label
                    class="upload-slot <?= $existingImage ? 'has-existing-image' : ''; ?>"
                    data-image-id="<?= $existingImage
                                      ? (int) $existingImage["id"]
                                      : ""; ?>">


                    <!-- FILE INPUT -->

                    <input
                      type="file"
                      class="file-input"
                      name="images[]"
                      accept=".jpg,.jpeg,.png,.webp"
                      aria-label="Upload property image <?= $i + 1; ?>">


                    <?php if ($existingImage): ?>


                      <!-- EXISTING IMAGE -->

                      <img
                        src="uploads/properties/<?= htmlspecialchars(
                                                  $existingImage["image"]
                                                ); ?>"
                        class="existing-image-preview"
                        loading="lazy"
                        decoding="async"
                        alt="Property image <?= $i + 1; ?>">


                      <!-- REMOVE -->

                      <button
                        type="button"
                        class="remove-existing-image"
                        data-image-id="<?= (int) $existingImage["id"]; ?>"
                        aria-label="Remove property image">

                        <i
                          class="ri-close-line"
                          aria-hidden="true">
                        </i>

                      </button>


                    <?php else: ?>


                      <!-- EMPTY SLOT -->

                      <div class="content">

                        <i
                          class="ri-camera-line"
                          aria-hidden="true">
                        </i>

                        <span>
                          Upload
                        </span>

                      </div>


                    <?php endif; ?>


                  </label>

                <?php endfor; ?>

              </div>

            </div>

            <!-- =================================
                     LOCATION
                ================================== -->

            <div class="custom-card mb-4">

              <h2 class="mb-3 h6">
                Location
              </h2>


              <div
                id="map"
                class="map"
                aria-label="Property location map">
              </div>


              <div class="map-search mt-3">

                <i
                  class="ri-search-line"
                  aria-hidden="true">
                </i>


                <input
                  id="searchInput"
                  type="text"
                  placeholder="Search address..."
                  aria-label="Search address"
                  autocomplete="off"
                  value="<?= htmlspecialchars(
                            $property["address"]
                          ); ?>">

              </div>


              <!-- LOCATION -->

              <input
                type="hidden"
                id="propertyAddress"
                name="address"
                value="<?= htmlspecialchars(
                          $property["address"]
                        ); ?>">


              <input
                type="hidden"
                id="propertyCanton"
                name="canton"
                value="<?= htmlspecialchars(
                          $property["canton"]
                        ); ?>">


              <input
                type="hidden"
                id="propertyLatitude"
                name="latitude"
                value="<?= htmlspecialchars(
                          $property["latitude"]
                        ); ?>">


              <input
                type="hidden"
                id="propertyLongitude"
                name="longitude"
                value="<?= htmlspecialchars(
                          $property["longitude"]
                        ); ?>">

            </div>


            <!-- =================================
                     ACTIONS
                ================================== -->

            <button
              type="submit"
              class="btn-save w-100">

              Update Property

            </button>


            <a
              href="dashboard.php"
              class="btnCancel d-block w-100 mt-2">

              Cancel

            </a>

          </div>

        </div>

      </div>

    </form>

  </div>



  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyArmldTdZMZdY_FMLmggX9bngTjkd9JPBI&libraries=places"></script>


  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    window.editPropertyData = <?= $propertyJson; ?>;
  </script>

  <script src="js/editProperty.js"></script>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js">
  </script>

</body>

</html>