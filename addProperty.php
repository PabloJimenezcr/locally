<?php

session_start();

if (!isset($_SESSION["id"])) {
  header("Location: login.php");
  exit;
}

require_once "includes/conexion.php";

$userId = (int) $_SESSION["id"];

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
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <title>Locally</title>
</head>

<body>


  <nav class="mobile-nav d-md-none" aria-label="Mobile navigation">

    <a href="dashboard.php" class="nav-item">
      <i class="ri-home-5-line" aria-hidden="true"></i>
      <span>Home</span>
    </a>

    <a href="addProperty.php" class="nav-item active" aria-current="page">
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


  <div class="container-fluid px-5 py-4 form-container">

    <form
      id="propertyForm"
      action="actions/createProperty.php"
      method="POST"
      enctype="multipart/form-data">

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

              <span class="step">01</span>

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
                required>

            </div>


            <!-- PROVINCE / PRICE / SIZE -->

            <div class="row mt-3 g-3">

              <!-- Province -->

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

                  <option value="" selected disabled>
                    Select province
                  </option>

                  <option value="San José">
                    San José
                  </option>

                  <option value="Alajuela">
                    Alajuela
                  </option>

                  <option value="Cartago">
                    Cartago
                  </option>

                  <option value="Heredia">
                    Heredia
                  </option>

                  <option value="Guanacaste">
                    Guanacaste
                  </option>

                  <option value="Puntarenas">
                    Puntarenas
                  </option>

                  <option value="Limón">
                    Limón
                  </option>

                </select>

              </div>


              <!-- Price -->

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
                  required>

              </div>


              <!-- Size -->

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
                    required>

                  <select
                    id="sizeUnit"
                    name="size_unit"
                    class="form-select custom-input">

                    <option value="m2">
                      M²
                    </option>

                    <option value="sqft">
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

              <span class="step">02</span>

              <h2 class="mb-0">
                Structure & Logistics
              </h2>

            </div>


            <div class="row mt-3 g-3">

              <!-- Property Type -->

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

                  <option value="" selected disabled>
                    Select type
                  </option>

                  <option value="house">
                    House
                  </option>

                  <option value="lot">
                    Lot
                  </option>

                  <option value="apartment">
                    Apartment
                  </option>

                </select>

              </div>


              <!-- Bedrooms -->

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

                    1

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
                  value="1">

              </div>


              <!-- Bathrooms -->

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

                    1

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
                  value="1">

              </div>

            </div>

          </div>


          <!-- =====================================
                     03 - FEATURES & NARRATIVE
                ====================================== -->

          <div class="custom-card mt-4">

            <div class="card-header-custom d-flex align-items-center gap-2">

              <span class="step">03</span>

              <h2 class="mb-0">
                Features & Narrative
              </h2>

            </div>


            <!-- CORE AMENITIES -->

            <div class="mt-3">

              <p class="label mb-2">
                Core Amenities
              </p>

              <div class="amenities d-flex gap-4 mt-2">

                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="parking">

                  <span></span>

                  Parking

                </label>


                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="wifi">

                  <span></span>

                  Wifi

                </label>


                <label class="custom-checkbox">

                  <input
                    type="checkbox"
                    name="amenities[]"
                    value="elevator">

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
              </div>


              <!-- TAGS PARA PHP -->

              <input
                type="hidden"
                id="propertyTags"
                name="tags"
                value="[]">

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
                required>
                        </textarea>

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
                    class="boxActive box active"
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
                    class="boxInactive box"
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


              <!-- STATUS PARA PHP -->

              <input
                type="hidden"
                id="propertyStatus"
                name="status"
                value="active">

            </div>


            <!-- =================================
                         MEDIA GALLERY
                    ================================== -->


            <div class="custom-card mb-4">

              <div class="d-flex justify-content-between align-items-center mb-2">

                <h2 class="mb-0 h6">
                  Media Gallery
                </h2>

                <span class="gallery-count">
                  1 required · 6 optional
                </span>

              </div>

              <p class="gallery-help">
                Add up to 7 photos. The first photo is required and will be used as the main image.
              </p>


              <div class="gallery">


                <!-- IMAGE 1 - REQUIRED -->

                <label class="upload-slot primary-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload primary property image"
                    required>

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Main photo
                    </span>

                  </div>

                </label>


                <!-- IMAGE 2 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 2">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>


                <!-- IMAGE 3 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 3">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>


                <!-- IMAGE 4 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 4">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>


                <!-- IMAGE 5 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 5">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>


                <!-- IMAGE 6 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 6">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>


                <!-- IMAGE 7 -->

                <label class="upload-slot">

                  <input
                    type="file"
                    class="file-input"
                    name="images[]"
                    accept=".jpg,.jpeg,.png,.webp"
                    aria-label="Upload property image 7">

                  <div class="content">

                    <i
                      class="ri-camera-line"
                      aria-hidden="true">
                    </i>

                    <span>
                      Upload
                    </span>

                  </div>

                </label>

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
                  autocomplete="off">

              </div>


              <!-- LOCATION PARA PHP -->

              <input
                type="hidden"
                id="propertyAddress"
                name="address">

              <input
                type="hidden"
                id="propertyCanton"
                name="canton">

              <input
                type="hidden"
                id="propertyLatitude"
                name="latitude">

              <input
                type="hidden"
                id="propertyLongitude"
                name="longitude">

            </div>


            <!-- =================================
                         ACTIONS
                    ================================== -->

            <button
              type="submit"
              class="btn-save w-100">

              Add Property

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

  <script src="js/addProperty.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

</body>

</html>