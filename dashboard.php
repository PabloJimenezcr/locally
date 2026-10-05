<?php

session_start();

if (!isset($_SESSION["id"])) {
  header("Location: login.php");
  exit;
}

require_once "includes/conexion.php";

$userId = (int) $_SESSION["id"];

/*==================================================
    OBTENER PROPIEDADES DEL USUARIO
==================================================*/

$stmt = $conn->prepare("
    SELECT
        p.id,
        p.name,
        p.price,
        p.province,
        p.canton,
        p.size,
        p.size_unit,
        p.property_type,
        p.bedrooms,
        p.bathrooms,
        p.status,
        p.address,

        (
            SELECT pi.image
            FROM property_images pi
            WHERE pi.property_id = p.id
            ORDER BY pi.id ASC
            LIMIT 1
        ) AS image

    FROM properties p

    WHERE p.user_id = ?

    ORDER BY p.created_at DESC
");

$stmt->bind_param("i", $userId);

$stmt->execute();

$result = $stmt->get_result();

$properties = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();


/*==================================================
    CONTADORES
==================================================*/

$countStmt = $conn->prepare("
    SELECT
        SUM(status = 'active') AS active_count,
        SUM(status = 'inactive') AS inactive_count
    FROM properties
    WHERE user_id = ?
");

$countStmt->bind_param("i", $userId);

$countStmt->execute();

$counts = $countStmt
  ->get_result()
  ->fetch_assoc();

$countStmt->close();

$activeCount = (int) ($counts["active_count"] ?? 0);
$inactiveCount = (int) ($counts["inactive_count"] ?? 0);


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Locally</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <link href=" https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>


  <nav class="mobile-nav d-md-none" aria-label="Mobile navigation">

    <a href="dashboard.php" class="nav-item active" aria-current="page">
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

  <div class="container-fluid px-5 py-4 main-container">

    <!-- =========================================
         HEADER
    ========================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

      <div class="mb-4">

        <h2 class="m-0 title">
          Properties Overview
        </h2>

        <p>
          Manage and monitor your curated estate collection.
        </p>

      </div>


      <div class="d-flex align-items-center gap-3">

        <a
          href="addProperty.php"
          class="btn btn-success add-btn">

          + Add Property

        </a>


        <!-- PROFILE -->

        <div class="dropdown avatar-dropdown">

          <button
            class="avatar border-0 bg-transparent p-0"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-label="Open profile menu">

            <img
              src="uploads/profiles/<?= htmlspecialchars($_SESSION["photo"]); ?>"
              class="img-fluid"
              alt="Foto de perfil">

          </button>


          <ul
            class="dropdown-menu dropdown-menu-end custom-dropdown dropdown-menu-lg-end">


            <li class="dropdown-header">

              <strong>
                <?= htmlspecialchars($_SESSION["name"]); ?>
              </strong>

              <small>
                <?= ucfirst(
                  htmlspecialchars($_SESSION["role"])
                ); ?>
              </small>

            </li>


            <li>

              <a
                class="dropdown-item"
                href="user.php">

                <i
                  class="ri-user-3-line"
                  aria-hidden="true">
                </i>

                Profile

              </a>

            </li>


            <li>

              <hr class="dropdown-divider">

            </li>


            <li>

              <a
                class="dropdown-item logout"
                href="actions/logout.php">

                <i
                  class="ri-logout-box-line"
                  aria-hidden="true">
                </i>

                Log out

              </a>

            </li>

          </ul>

        </div>

      </div>

    </div>


    <!-- =========================================
         PROPERTY COUNTERS
    ========================================== -->

    <div class="row g-4">

      <!-- ACTIVE -->

      <div class="col-md-6">

        <div
          class="property-card d-flex justify-content-between align-items-center activeProperty">

          <div>

            <p class="card-label">
              Active Properties
            </p>

            <h3 class="card-number">
              <?= $activeCount; ?>
            </h3>

          </div>


          <div class="icon-box active">

            <i
              class="ri-home-9-line"
              aria-hidden="true">
            </i>

          </div>

        </div>

      </div>


      <!-- INACTIVE -->

      <div class="col-md-6">

        <div
          class="property-card d-flex justify-content-between align-items-center inactiveProperty">

          <div>

            <p class="card-label">
              Inactive Properties
            </p>

            <h3 class="card-numberInactive">
              <?= str_pad(
                $inactiveCount,
                2,
                "0",
                STR_PAD_LEFT
              ); ?>
            </h3>

          </div>


          <div class="icon-box inactive">

            <i
              class="ri-archive-line"
              aria-hidden="true">
            </i>

          </div>

        </div>

      </div>

    </div>


    <!-- =========================================
         SEARCH
    ========================================== -->

    <div class="row">

      <div class="search-container mt-4">

        <div class="search-box d-flex align-items-center">

          <i
            class="ri-search-line search-icon"
            aria-hidden="true">
          </i>


          <input
            id="propertySearch"
            type="text"
            placeholder="Search properties..."
            aria-label="Search properties"
            autocomplete="off">

        </div>

      </div>

    </div>


    <!-- =========================================
         PROPERTIES
    ========================================== -->

    <div class="row g-4 pt-5 properties-container">

      <?php if (empty($properties)): ?>

        <!-- =====================================
                 EMPTY STATE
            ====================================== -->

        <div class="col-12">

          <div class="text-center py-5">

            <i
              class="ri-home-5-line"
              style="font-size:50px;"
              aria-hidden="true">
            </i>


            <h3 class="mt-3">

              No tienes propiedades todavía

            </h3>


            <p>

              Agrega tu primera propiedad para comenzar.

            </p>


            <a
              href="addProperty.php"
              class="btn btn-success">

              + Add Property

            </a>

          </div>

        </div>


      <?php else: ?>


        <?php foreach ($properties as $property): ?>

          <?php

          /*=========================================
                    DATA DE LA PROPIEDAD
                =========================================*/

          $image = !empty($property["image"])
            ? "uploads/properties/" .
            $property["image"]
            : "img/cardHome.jpg";


          $status =
            $property["status"];


          $statusLabel =
            $status === "active"
            ? "Active"
            : "Inactive";


          $sizeUnit =
            $property["size_unit"] === "sqft"
            ? "sqft"
            : "m²";


          /*
                 * Datos normalizados para la búsqueda.
                 */

          $searchName =
            strtolower(
              $property["name"]
            );


          $searchCanton =
            strtolower(
              $property["canton"]
            );


          $searchProvince =
            strtolower(
              $property["province"]
            );


          $searchStatus =
            strtolower(
              $property["status"]
            );

          ?>


          <!-- =====================================
                     PROPERTY ITEM
                ====================================== -->

          <div
            class="col-xl-4 col-lg-6 col-md-6 property-item"

            data-name="<?= htmlspecialchars(
                          $searchName
                        ); ?>"

            data-canton="<?= htmlspecialchars(
                            $searchCanton
                          ); ?>"

            data-province="<?= htmlspecialchars(
                              $searchProvince
                            ); ?>"

            data-status="<?= htmlspecialchars(
                            $searchStatus
                          ); ?>">


            <!-- =================================
                         PROPERTY WRAPPER
                    ================================== -->

            <div class="propertyCard-wrapper">


              <!-- =================================
                             PROPERTY CARD
                        ================================== -->

              <a
                href="propertyAdmin.php?id=<?= (int) $property["id"]; ?>"
                class="propertyCard">


                <!-- IMAGE -->

                <div
                  class="propertyImg d-flex justify-content-center align-items-center">

                  <img
                    src="<?= htmlspecialchars($image); ?>"
                    loading="lazy"
                    decoding="async"
                    alt="<?= htmlspecialchars(
                            $property["name"]
                          ); ?>">


                  <!-- STATUS -->

                  <div class="badges">

                    <p
                      class="<?= $status === "active"
                                ? "featured"
                                : "inactive"; ?>">

                      <?= $statusLabel; ?>

                    </p>

                  </div>


                  <!-- LOCATION -->

                  <div class="location">

                    <i
                      class="ri-map-pin-2-line"
                      aria-hidden="true">
                    </i>

                    <?= htmlspecialchars(
                      $property["canton"]
                    ); ?>,

                    <?= htmlspecialchars(
                      $property["province"]
                    ); ?>

                  </div>

                </div>


                <!-- =================================
                                 BODY
                            ================================== -->

                <div class="bodyCard">


                  <!-- NAME -->

                  <h3>

                    <?= htmlspecialchars(
                      $property["name"]
                    ); ?>

                  </h3>


                  <!-- FEATURES -->

                  <div class="propertyFeatures">


                    <!-- BEDROOMS -->

                    <p>

                      <i
                        class="ri-hotel-bed-line"
                        aria-hidden="true">
                      </i>

                      <?= (int) $property["bedrooms"]; ?>

                      <?= $property["bedrooms"] == 1
                        ? "Bed"
                        : "Beds"; ?>

                    </p>


                    <!-- BATHROOMS -->

                    <p>

                      <i
                        class="ri-home-gear-line"
                        aria-hidden="true">
                      </i>

                      <?= (int) $property["bathrooms"]; ?>

                      <?= $property["bathrooms"] == 1
                        ? "Bath"
                        : "Baths"; ?>

                    </p>


                    <!-- SIZE -->

                    <p>

                      <i
                        class="ri-ruler-line"
                        aria-hidden="true">
                      </i>

                      <?= htmlspecialchars(
                        $property["size"]
                      ); ?>

                      <?= $sizeUnit; ?>

                    </p>

                  </div>


                  <!-- =================================
                                     FOOTER
                                ================================== -->

                  <div class="propertyFooter">


                    <!-- USER -->

                    <div class="agent">

                      <img
                        src="uploads/profiles/<?= htmlspecialchars(
                                                $_SESSION["photo"]
                                              ); ?>"
                        loading="lazy"
                        decoding="async"
                        alt="Foto de perfil de <?= htmlspecialchars(
                                                  $_SESSION["name"]
                                                ); ?>">

                      <span>

                        <?= htmlspecialchars(
                          $_SESSION["name"]
                        ); ?>

                      </span>

                    </div>


                    <!-- PRICE -->

                    <span class="price">

                      $

                      <?= number_format(
                        (float) $property["price"],
                        2
                      ); ?>

                    </span>

                  </div>

                </div>

              </a>


              <!-- =================================
                             EDIT PROPERTY
                        ================================== -->

              <a
                href="editProperty.php?id=<?= (int) $property["id"]; ?>"
                class="edit-property"
                title="Editar propiedad"
                aria-label="Editar <?= htmlspecialchars(
                                      $property["name"]
                                    ); ?>">

                <i
                  class="ri-pencil-line"
                  aria-hidden="true">
                </i>

              </a>


            </div>

          </div>


        <?php endforeach; ?>


      <?php endif; ?>

    </div>


    <!-- =========================================
         NO SEARCH RESULTS
    ========================================== -->

    <div
      id="noSearchResults"
      class="text-center py-5 d-none">

      <i
        class="ri-search-eye-line"
        style="font-size:50px;"
        aria-hidden="true">
      </i>


      <h4 class="mt-3">

        No encontramos propiedades

      </h4>


      <p>

        Prueba con otro nombre, cantón o provincia.

      </p>

    </div>


  </div>


  <script src="js/searchProperty.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>



</body>

</html>