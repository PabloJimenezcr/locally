<?php

require_once "includes/config.php";
require_once "includes/conexion.php";

$token = trim($_GET["token"] ?? "");

$tokenValid = false;

if (!empty($token)) {

    $tokenHash = hash("sha256", $token);

    $stmt = $conn->prepare("
        SELECT id, expires_at
        FROM password_resets
        WHERE token = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $tokenHash);

    $stmt->execute();

    $result = $stmt->get_result();

    $reset = $result->fetch_assoc();

    $stmt->close();

    if ($reset) {

        if (strtotime($reset["expires_at"]) >= time()) {
            $tokenValid = true;
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Restablecer contraseña | Locally
    </title>

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
        href="css/loginR.css">

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
        rel="stylesheet">

</head>

<body>

    <div class="container-auth">

        <div class="form-container sign-in reset-password">

            <?php if ($tokenValid): ?>

                <form id="resetPasswordForm">

                    <h2>Reset Password</h2>

                    <div class="input-group">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder=" ">

                        <label for="password">
                            New Password
                        </label>

                        <button
                            type="button"
                            class="toggle-pass"
                            onclick="togglePassword(this, 'password')"
                            aria-label="Show password">

                            <i class="ri-eye-line"></i>

                        </button>

                    </div>

                    <div class="input-group">

                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder=" ">

                        <label for="confirmPassword">
                            Confirm Password
                        </label>

                        <button
                            type="button"
                            class="toggle-pass"
                            onclick="togglePassword(this, 'confirmPassword')"
                            aria-label="Show password">

                            <i class="ri-eye-line"></i>

                        </button>

                    </div>

                    <input
                        type="hidden"
                        name="token"
                        value="<?= htmlspecialchars($token); ?>">

                    <button type="submit">
                        Update Password
                    </button>

                </form>

            <?php else: ?>

                <div class="reset-error">

                    <h2>Link inválido</h2>

                    <p>
                        Este enlace no es válido o ya expiró.
                    </p>

                    <a href="login.php">
                        Volver al inicio de sesión
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($tokenValid): ?>

        <script>
            function togglePassword(button, id) {

                const input = document.getElementById(id);
                const icon = button.querySelector("i");

                if (input.type === "password") {

                    input.type = "text";

                    icon.classList.replace(
                        "ri-eye-line",
                        "ri-eye-off-line"
                    );

                } else {

                    input.type = "password";

                    icon.classList.replace(
                        "ri-eye-off-line",
                        "ri-eye-line"
                    );

                }

            }

            document
                .getElementById("resetPasswordForm")
                .addEventListener("submit", async function(e) {

                    e.preventDefault();

                    const password =
                        document.getElementById("password");

                    const confirmPassword =
                        document.getElementById("confirmPassword");

                    if (!password.value.trim()) {

                        Swal.fire({
                            icon: "warning",
                            title: "Campo requerido",
                            text: "Ingresa tu nueva contraseña."
                        });

                        return;
                    }

                    if (!confirmPassword.value.trim()) {

                        Swal.fire({
                            icon: "warning",
                            title: "Campo requerido",
                            text: "Confirma tu nueva contraseña."
                        });

                        return;
                    }

                    if (password.value.length < 8) {

                        Swal.fire({
                            icon: "warning",
                            title: "Contraseña débil",
                            text: "Debe tener mínimo 8 caracteres."
                        });

                        return;
                    }

                    if (!/[A-Z]/.test(password.value)) {

                        Swal.fire({
                            icon: "warning",
                            title: "Contraseña débil",
                            text: "Debe contener una letra mayúscula."
                        });

                        return;
                    }

                    if (!/[0-9]/.test(password.value)) {

                        Swal.fire({
                            icon: "warning",
                            title: "Contraseña débil",
                            text: "Debe contener un número."
                        });

                        return;
                    }

                    if (password.value !== confirmPassword.value) {

                        Swal.fire({
                            icon: "warning",
                            title: "Las contraseñas no coinciden",
                            text: "Verifica ambas contraseñas."
                        });

                        return;
                    }

                    const response = await fetch(
                        "actions/resetPassword.php", {
                            method: "POST",
                            body: new FormData(this)
                        }
                    );

                    const result =
                        (await response.text()).trim();

                    switch (result) {

                        case "success":

                            await Swal.fire({
                                icon: "success",
                                title: "Contraseña actualizada",
                                text: "Ya puedes iniciar sesión con tu nueva contraseña.",
                                confirmButtonColor: "#198754"
                            });

                            window.location.href = "login.php";

                            break;

                        case "invalid_token":

                            Swal.fire({
                                icon: "error",
                                title: "Enlace inválido",
                                text: "Este enlace ya no es válido."
                            });

                            break;

                        case "expired_token":

                            Swal.fire({
                                icon: "error",
                                title: "Enlace expirado",
                                text: "Solicita nuevamente la recuperación de contraseña."
                            });

                            break;

                        case "password_mismatch":

                            Swal.fire({
                                icon: "warning",
                                title: "Contraseñas diferentes",
                                text: "Las contraseñas no coinciden."
                            });

                            break;

                        case "weak_password":

                            Swal.fire({
                                icon: "warning",
                                title: "Contraseña débil",
                                text: "Usa mínimo 8 caracteres, una mayúscula y un número."
                            });

                            break;

                        default:

                            console.log(result);

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "No fue posible actualizar la contraseña."
                            });

                    }

                });
        </script>

    <?php endif; ?>

</body>

</html>