<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}


require_once("includes/conexion.php");

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

$propertyStmt = $conn->prepare("
    SELECT COUNT(*) AS total_properties
    FROM properties
    WHERE user_id = ?
");

$propertyStmt->bind_param("i", $_SESSION["id"]);
$propertyStmt->execute();

$propertyResult = $propertyStmt->get_result();
$propertyCount = $propertyResult->fetch_assoc()["total_properties"];

$propertyStmt->close();

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
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Perfil</title>
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

        <a href="user.php" class="nav-item active">
            <i class="ri-user-3-line" aria-hidden="true"></i>
            <span>Account</span>
        </a>

        <a href="actions/logout.php" class="nav-item">
            <i class="ri-logout-box-line" aria-hidden="true"></i>
            <span>Log out</span>

        </a>

    </nav>


    <header class="container px-5 py-4">

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



    <section class="profile-page py-5">

        <div class="container">

            <form
                id="updateProfileForm"
                enctype="multipart/form-data">

                <div class="row g-5">

                    <!-- SIDEBAR -->

                    <div class="col-lg-3">

                        <div class="profile-sidebar">

                            <div class="profile-image">

                                <img
                                    id="profilePreview"
                                    src="uploads/profiles/<?php echo htmlspecialchars($user["photo"]); ?>"
                                    class="img-fluid"
                                    alt="Foto de perfil">

                                <input
                                    type="file"
                                    id="profilePhoto"
                                    name="photo"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    hidden>

                                <button
                                    type="button"
                                    class="change-photo"
                                    id="changePhotoBtn">

                                    <i class="ri-camera-line"></i>

                                </button>

                            </div>



                            <h3
                                class="mt-4"
                                id="profileName">

                                <?= htmlspecialchars($user["name"]); ?>

                            </h3>

                            <div class="summary-card mt-4">

                                <h6>Resumen de Cuenta</h6>

                                <div class="d-flex justify-content-between mt-4">

                                    <span>Miembro desde</span>

                                    <strong>
                                        <?= date("M Y", strtotime($user["created_at"])); ?>
                                    </strong>

                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <span>Propiedades</span>
                                    <strong><?= (int) $propertyCount; ?></strong>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- CONTENIDO -->

                    <div class="col-lg-9">


                        <div class="d-flex align-items-center gap-3 justify-content-between">


                            <h1>Mi perfil</h1>

                        </div>


                        <div class="section-card">
                            <h4>
                                <i class="ri-user-line"></i>
                                Información del Perfil
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user["name"]); ?>">
                                </div>

                                <div class="col-md-6">

                                    <label>Email</label>

                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user["email"]); ?>">

                                </div>

                            </div>

                        </div>

                        <div class="d-flex align-items-center gap-4 mt-5">

                            <button
                                type="submit"
                                class="btn btn-save">

                                Actualizar

                                <i class="bi bi-check-circle ms-2"></i>

                            </button>

                        </div>

                        <!-- SEGURIDAD -->

                        <div class="section-card mt-5">

                            <h4>

                                <i class="ri-shield-keyhole-line"></i>

                                Seguridad de la Cuenta

                            </h4>

                            <div class="security-box">

                                <div>

                                    <label>Contraseña</label>

                                    <div class="password-preview">

                                        ••••••••••••••••

                                    </div>

                                    <small>

                                        Por seguridad tu contraseña nunca se muestra.

                                    </small>

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-change-password"
                                    data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal">

                                    <i class="ri-lock-password-line"></i>

                                    Cambiar contraseña

                                </button>

                            </div>

                        </div>


                        <div class="danger-zone mt-5 mb-5">

                            <div>

                                <h5>Zona de Peligro</h5>

                                <p>
                                    Eliminar tu cuenta es una acción permanente y no se puede deshacer.
                                </p>

                            </div>

                            <button
                                id="deleteAccountBtn"
                                type="button"
                                class="btn btn-outline-danger">

                                Eliminar cuenta

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </section>


    <div class="modal fade" id="changePasswordModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Cambiar contraseña
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <form id="changePasswordForm">

                    <div class="modal-body">

                        <div class="mb-4">

                            <label>Contraseña actual</label>

                            <div class="password-input">

                                <input
                                    type="password"
                                    class="form-control password-field"
                                    id="currentPassword"
                                    name="currentPassword">

                                <button
                                    type="button"
                                    class="toggle-password">

                                    <i class="ri-eye-line"></i>

                                </button>

                            </div>

                        </div>

                        <div class="mb-4">

                            <label>Nueva contraseña</label>

                            <div class="password-input">

                                <input
                                    type="password"
                                    class="form-control password-field"
                                    id="newPassword"
                                    name="newPassword">

                                <button
                                    type="button"
                                    class="toggle-password">

                                    <i class="ri-eye-line"></i>

                                </button>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label>Confirmar contraseña</label>

                            <div class="password-input">

                                <input
                                    type="password"
                                    class="form-control password-field"
                                    id="confirmPassword"
                                    name="confirmPassword">

                                <button
                                    type="button"
                                    class="toggle-password">

                                    <i class="ri-eye-line"></i>

                                </button>

                            </div>

                        </div>

                        <div class="password-strength">

                            <div class="strength-bar"></div>

                            <small>
                                Usa mínimo 8 caracteres, una mayúscula y un número.
                            </small>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            class="btn btn-light"
                            type="button"
                            data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="btn btn-save">

                            Actualizar contraseña

                        </button>

                    </div>

                </form>



            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/profile.js"></script>


</body>

</html>