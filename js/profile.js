/*==================================================
    HELPERS
==================================================*/

const SUCCESS = "#198754";

function showAlert(icon, title, text) {

    return Swal.fire({
        icon,
        title,
        text,
        confirmButtonColor: SUCCESS
    });

}

const warn = (title, text) =>
    showAlert("warning", title, text);

const error = (title, text) =>
    showAlert("error", title, text);

const success = (title, text) =>
    showAlert("success", title, text);

/*==================================================
    FETCH
==================================================*/

async function postForm(url, form) {

    const response = await fetch(url, {
        method: "POST",
        body: new FormData(form)
    });

    return (await response.text()).trim();

}

/*==================================================
    PASSWORD
==================================================*/

document.querySelectorAll(".toggle-password").forEach(btn => {

    btn.addEventListener("click", () => {

        const input = btn.previousElementSibling;
        const icon = btn.querySelector("i");

        const visible = input.type === "password";

        input.type = visible ? "text" : "password";

        icon.className = visible
            ? "ri-eye-off-line"
            : "ri-eye-line";

    });

});

/*==================================================
    FOTO DE PERFIL
==================================================*/

const profilePhoto = document.getElementById("profilePhoto");
const profilePreview = document.getElementById("profilePreview");
const changePhotoBtn = document.getElementById("changePhotoBtn");

changePhotoBtn.addEventListener("click", () => profilePhoto.click());

profilePhoto.addEventListener("change", () => {

    const file = profilePhoto.files[0];

    if (!file) return;

    const validTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (!validTypes.includes(file.type)) {

        profilePhoto.value = "";

        return warn(
            "Imagen inválida",
            "Solo puedes subir imágenes JPG, PNG o WEBP."
        );

    }

    if (file.size > 5 * 1024 * 1024) {

        profilePhoto.value = "";

        return warn(
            "Imagen demasiado grande",
            "La imagen no puede superar los 5 MB."
        );

    }

    profilePreview.src = URL.createObjectURL(file);

});

/*==================================================
    ACTUALIZAR PERFIL
==================================================*/

const updateProfileForm = document.getElementById("updateProfileForm");

if (updateProfileForm) {

    updateProfileForm.addEventListener("submit", async function (e) {

        e.preventDefault();

        const name = document.getElementById("name");
        const email = document.getElementById("email");

        if (!name.value.trim())
            return warn(
                "Nombre requerido",
                "Debes ingresar tu nombre."
            );

        if (name.value.trim().length < 3)
            return warn(
                "Nombre inválido",
                "Debe contener al menos 3 caracteres."
            );

        if (name.value.trim().length > 60)
            return warn(
                "Nombre muy largo",
                "El nombre no puede superar los 60 caracteres."
            );

        if (!email.value.trim())
            return warn(
                "Correo requerido",
                "Debes ingresar un correo electrónico."
            );

        if (!email.checkValidity())
            return warn(
                "Correo inválido",
                "Ingresa un correo válido."
            );

        const result = await postForm(
            "actions/updateProfile.php",
            this
        );

        switch (result) {

            case "success":

                document.getElementById("profileName").textContent = name.value;

                await success(
                    "Perfil actualizado",
                    "Los datos se actualizaron correctamente."
                );

                break;

            case "email_exists":

                error(
                    "Correo existente",
                    "Ese correo ya pertenece a otro usuario."
                );

                break;

            case "invalid_name":

                warn(
                    "Nombre inválido",
                    "El nombre ingresado no es válido."
                );

                break;

            case "invalid_email":

                warn(
                    "Correo inválido",
                    "El correo ingresado no es válido."
                );

                break;

            case "invalid_image":

                warn(
                    "Imagen inválida",
                    "Solo se permiten imágenes JPG, PNG o WEBP."
                );

                break;

            case "large_image":

                warn(
                    "Imagen muy grande",
                    "La imagen no puede superar los 5 MB."
                );

                break;

            case "upload_error":

                error(
                    "Error",
                    "No fue posible subir la imagen."
                );

                break;

            case "empty_fields":

                warn(
                    "Campos vacíos",
                    "Completa toda la información."
                );

                break;


            case "no_changes":

                warn(
                    "Sin cambios",
                    "No realizaste ninguna modificación."
                );


                break;

            default:

                console.log(result);

                error(
                    "Error",
                    "Ocurrió un error inesperado."
                );

        }

    });

}

/*==================================================
    CAMBIAR CONTRASEÑA
==================================================*/

const changePasswordForm = document.getElementById("changePasswordForm");

if (changePasswordForm) {

    changePasswordForm.addEventListener("submit", async function (e) {

        e.preventDefault();

        const current = document.getElementById("currentPassword");
        const password = document.getElementById("newPassword");
        const confirm = document.getElementById("confirmPassword");

        if (!current.value.trim())
            return warn(
                "Campo requerido",
                "Ingresa tu contraseña actual."
            );

        if (!password.value.trim())
            return warn(
                "Campo requerido",
                "Ingresa la nueva contraseña."
            );

        if (password.value.length < 8)
            return warn(
                "Contraseña débil",
                "Debe tener mínimo 8 caracteres."
            );

        if (!/[A-Z]/.test(password.value))
            return warn(
                "Contraseña débil",
                "Debe contener una letra mayúscula."
            );

        if (!/[0-9]/.test(password.value))
            return warn(
                "Contraseña débil",
                "Debe contener un número."
            );

        if (password.value !== confirm.value)
            return warn(
                "Contraseñas diferentes",
                "Las contraseñas no coinciden."
            );

        if (password.value === current.value)
            return warn(
                "Contraseña inválida",
                "La nueva contraseña debe ser diferente."
            );

        const result = await postForm(
            "actions/changePassword.php",
            this
        );

        switch (result) {

            case "success":

                await success(
                    "Contraseña actualizada",
                    "La contraseña se actualizó correctamente."
                );

                this.reset();

                bootstrap.Modal.getInstance(
                    document.getElementById("changePasswordModal")
                ).hide();

                break;

            case "wrong_password":

                error(
                    "Contraseña incorrecta",
                    "La contraseña actual no coincide."
                );

                break;

            case "password_mismatch":

                warn(
                    "Contraseñas distintas",
                    "Las contraseñas no coinciden."
                );

                break;

            case "same_password":

                warn(
                    "Contraseña inválida",
                    "La nueva contraseña debe ser diferente."
                );

                break;

            case "weak_password":

                warn(
                    "Contraseña débil",
                    "Debe contener mínimo 8 caracteres, una mayúscula y un número."
                );

                break;

            case "empty_fields":

                warn(
                    "Campos vacíos",
                    "Completa todos los campos."
                );

                break;

            default:

                console.log(result);

                error(
                    "Error",
                    "Ocurrió un error inesperado."
                );

        }

    });

}

/*==================================================
    ELIMINAR CUENTA
==================================================*/

const deleteAccountBtn = document.getElementById("deleteAccountBtn");

if (deleteAccountBtn) {

    deleteAccountBtn.addEventListener("click", async () => {

        // Confirmación
        const confirmDelete = await Swal.fire({

            icon: "warning",

            title: "¿Eliminar cuenta?",

            html: `
                Esta acción es <b>permanente</b>.<br>
                Se eliminarán tu perfil, foto y toda tu información.<br><br>
                <b>Esta acción no se puede deshacer.</b>
            `,

            showCancelButton: true,

            confirmButtonText: "Sí, eliminar",

            cancelButtonText: "Cancelar",

            confirmButtonColor: "#dc3545",

            cancelButtonColor: "#6c757d"

        });

        if (!confirmDelete.isConfirmed) return;

        // Pedir contraseña
        const {
            value: password
        } = await Swal.fire({

            title: "Confirma tu contraseña",

            input: "password",

            inputPlaceholder: "Contraseña",

            inputAttributes: {
                autocapitalize: "off",
                autocomplete: "current-password"
            },

            showCancelButton: true,

            confirmButtonText: "Eliminar cuenta",

            cancelButtonText: "Cancelar",

            confirmButtonColor: "#dc3545",

            inputValidator: (value) => {

                if (!value) {

                    return "Debes ingresar tu contraseña.";

                }

            }

        });

        if (!password) return;

        const formData = new FormData();

        formData.append("password", password);

        const response = await fetch(
            "actions/deleteAccount.php",
            {
                method: "POST",
                body: formData
            }
        );

        const result = (await response.text()).trim();

        switch (result) {

            case "success":

                await Swal.fire({

                    icon: "success",

                    title: "Cuenta eliminada",

                    text: "Tu cuenta fue eliminada correctamente.",

                    confirmButtonColor: "#198754"

                });

                window.location = "login.php";

                break;

            case "wrong_password":

                error(
                    "Contraseña incorrecta",
                    "La contraseña ingresada no coincide."
                );

                break;

            case "empty_password":

                warn(
                    "Campo requerido",
                    "Debes ingresar tu contraseña."
                );

                break;

            case "not_logged":

                error(
                    "Sesión expirada",
                    "Debes iniciar sesión nuevamente."
                );

                window.location = "login.php";

                break;

            case "user_not_found":

                error(
                    "Usuario no encontrado",
                    "No fue posible encontrar tu cuenta."
                );

                break;

            default:

                console.log(result);

                error(
                    "Error",
                    "Ocurrió un error al eliminar la cuenta."
                );

        }

    });

}