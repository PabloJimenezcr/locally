/* ===========================
   CONFIGURACIÓN GLOBAL
=========================== */

const SWAL_CONFIG = {
    confirmButtonColor: "#334d20",
    cancelButtonColor: "#6c757d"
};


/* ===========================
   CAMBIAR LOGIN / REGISTER
=========================== */

function toggleAuth() {
    document.getElementById("container").classList.toggle("active");
}


/* ===========================
   MOSTRAR / OCULTAR PASSWORD
=========================== */

function togglePass(button, id) {

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


/* ===========================
   VALIDAR CAMPO
=========================== */

function validateInput(input, isRegister = false) {

    const value = input.value.trim();


    /* Nombre */

    if (input.name === "name") {

        if (value === "") {
            return "Debes ingresar tu nombre.";
        }

        if (value.length < 3) {
            return "El nombre debe tener al menos 3 caracteres.";
        }

    }


    /* Email */

    if (input.type === "email") {

        if (value === "") {
            return "Debes ingresar un correo electrónico.";
        }

        if (!input.checkValidity()) {
            return "El correo electrónico no es válido.";
        }

    }


    /* Password */

    if (input.type === "password") {

        if (value === "") {
            return "Debes ingresar una contraseña.";
        }


        /* Validaciones únicamente para registro */

        if (isRegister) {

            if (value.length < 6) {
                return "La contraseña debe tener al menos 6 caracteres.";
            }

            if (!/[A-Z]/.test(value)) {
                return "La contraseña debe contener una letra mayúscula.";
            }

            if (!/[0-9]/.test(value)) {
                return "La contraseña debe contener un número.";
            }

        }

    }


    return null;

}


/* ===========================
   VISTA PREVIA FOTO
=========================== */

const profilePhoto = document.getElementById("profilePhoto");
const profilePreview = document.getElementById("profilePreview");
const profilePreviewBox = document.querySelector(".profile-preview");


if (profilePhoto) {

    profilePhoto.addEventListener("change", function () {

        const file = this.files[0];


        if (!file) {

            profilePreview.src = "";
            profilePreviewBox.classList.remove("has-image");

            return;

        }


        if (!file.type.startsWith("image/")) {

            Swal.fire({
                ...SWAL_CONFIG,
                icon: "warning",
                title: "Imagen inválida",
                text: "Solo se permiten imágenes."
            });

            this.value = "";

            return;

        }


        const imageURL = URL.createObjectURL(file);

        profilePreview.src = imageURL;

        profilePreviewBox.classList.add("has-image");


        profilePreview.onload = () => {

            URL.revokeObjectURL(imageURL);

        };

    });

}


/* ===========================
   REGISTRO
=========================== */

const registerForm = document.getElementById("registerForm");


if (registerForm) {

    registerForm.addEventListener(
        "submit",
        async function (e) {

            e.preventDefault();


            /* VALIDAR CAMPOS */

            const inputs = document.querySelectorAll(
                ".sign-up input:not([type='file'])"
            );


            for (const input of inputs) {

                const error = validateInput(
                    input,
                    true
                );


                if (error) {

                    Swal.fire({
                        ...SWAL_CONFIG,
                        icon: "warning",
                        title: "Verifica la información",
                        text: error
                    });


                    input.focus();

                    return;

                }

            }


            /* VALIDAR FOTO */

            const file = profilePhoto?.files[0];


            if (!file) {

                Swal.fire({
                    ...SWAL_CONFIG,
                    icon: "warning",
                    title: "Foto requerida",
                    text: "Debes seleccionar una foto de perfil."
                });

                return;

            }


            if (file.size > 5 * 1024 * 1024) {

                Swal.fire({
                    ...SWAL_CONFIG,
                    icon: "warning",
                    title: "Imagen muy grande",
                    text: "La imagen no puede superar los 5 MB."
                });

                return;

            }


            try {

                const response = await fetch(
                    "actions/register.php",
                    {
                        method: "POST",
                        body: new FormData(this)
                    }
                );


                const result = (
                    await response.text()
                ).trim();


                console.log("Register:", result);


                switch (result) {


                    case "success":

                        await Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "success",
                            title: "¡Cuenta creada!",
                            text: "Ya puedes iniciar sesión."
                        });


                        this.reset();

                        profilePreview.src = "";

                        profilePreviewBox.classList.remove(
                            "has-image"
                        );

                        toggleAuth();

                        break;


                    case "email_exists":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Correo registrado",
                            text: "Ese correo ya está en uso."
                        });

                        break;


                    case "invalid_image":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Imagen inválida",
                            text: "Solo se permiten JPG, JPEG, PNG y WEBP."
                        });

                        break;


                    case "upload_error":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Error",
                            text: "No fue posible subir la imagen."
                        });

                        break;


                    default:

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Error",
                            text: "Ocurrió un error inesperado."
                        });

                }


            } catch (error) {

                console.error("Register error:", error);


                Swal.fire({
                    ...SWAL_CONFIG,
                    icon: "error",
                    title: "Error de conexión",
                    text: "No se pudo contactar con el servidor."
                });

            }

        }
    );

}


/* ===========================
   LOGIN
=========================== */

const loginForm = document.getElementById("loginForm");


if (loginForm) {

    loginForm.addEventListener(
        "submit",
        async function (e) {

            e.preventDefault();


            /* VALIDAR CAMPOS */

            const inputs = document.querySelectorAll(
                ".sign-in input"
            );


            for (const input of inputs) {

                const error = validateInput(
                    input,
                    false
                );


                if (error) {

                    Swal.fire({
                        ...SWAL_CONFIG,
                        icon: "warning",
                        title: "Verifica la información",
                        text: error
                    });


                    input.focus();

                    return;

                }

            }


            try {

                const response = await fetch(
                    "actions/login.php",
                    {
                        method: "POST",
                        body: new FormData(this)
                    }
                );


                const result = (
                    await response.text()
                ).trim();


                console.log("Login:", result);


                switch (result) {


                    case "success":

                        window.location = "dashboard.php";

                        break;


                    case "empty_fields":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "warning",
                            title: "Campos vacíos",
                            text: "Completa todos los campos."
                        });

                        break;


                    case "user_not_found":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Usuario no encontrado",
                            text: "No existe una cuenta con ese correo."
                        });

                        break;


                    case "wrong_password":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Contraseña incorrecta",
                            text: "La contraseña ingresada no es correcta."
                        });

                        break;


                    default:

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Error",
                            text: "Ocurrió un error inesperado."
                        });

                }


            } catch (error) {

                console.error("Login error:", error);


                Swal.fire({
                    ...SWAL_CONFIG,
                    icon: "error",
                    title: "Error de conexión",
                    text: "No se pudo contactar con el servidor."
                });

            }

        }
    );

}


/* ===========================
   RECUPERAR CONTRASEÑA
=========================== */

const forgotPasswordBtn = document.getElementById(
    "forgotPasswordBtn"
);


if (forgotPasswordBtn) {

    forgotPasswordBtn.addEventListener(
        "click",
        async function (e) {

            e.preventDefault();


            /* ==========================
               SOLICITAR CORREO
            ========================== */

            const {
                value: email
            } = await Swal.fire({

                ...SWAL_CONFIG,

                title: "Recuperar contraseña",

                text: "Ingresa el correo asociado a tu cuenta.",

                input: "email",

                inputPlaceholder: "correo@ejemplo.com",

                showCancelButton: true,

                confirmButtonText: "Enviar enlace",

                cancelButtonText: "Cancelar",

                inputAttributes: {
                    autocomplete: "email"
                },

                inputValidator: (value) => {

                    if (!value) {

                        return "Debes ingresar tu correo.";

                    }


                    if (
                        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                            value
                        )
                    ) {

                        return "Ingresa un correo válido.";

                    }

                }

            });


            if (!email) {
                return;
            }


            /* ==========================
               LOADING
            ========================== */

            Swal.fire({

                title: "Procesando...",

                text: "Estamos preparando tu solicitud.",

                allowOutsideClick: false,

                allowEscapeKey: false,

                didOpen: () => {

                    Swal.showLoading();

                }

            });


            /* ==========================
               PETICIÓN
            ========================== */

            const controller = new AbortController();

            const timeout = setTimeout(
                () => controller.abort(),
                30000
            );


            try {

                const formData = new FormData();

                formData.append(
                    "email",
                    email.trim()
                );


                const response = await fetch(
                    "actions/forgotPassword.php",
                    {
                        method: "POST",
                        body: formData,
                        signal: controller.signal
                    }
                );


                clearTimeout(timeout);


                const result = (
                    await response.text()
                ).trim();


                console.log(
                    "Forgot password:",
                    result
                );


                Swal.close();


                /* ==========================
                   RESPUESTAS
                ========================== */

                switch (result) {


                    case "success":

                        await Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "success",
                            title: "Solicitud enviada",
                            text: "Si existe una cuenta asociada a ese correo, recibirás un enlace para restablecer tu contraseña."
                        });

                        break;


                    case "mail_error":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "No se pudo enviar el correo",
                            text: "Ocurrió un problema al enviar el enlace. Inténtalo nuevamente."
                        });

                        break;


                    case "server_error":

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Error del servidor",
                            text: "No fue posible procesar la solicitud."
                        });

                        break;


                    default:

                        Swal.fire({
                            ...SWAL_CONFIG,
                            icon: "error",
                            title: "Error",
                            text: "Ocurrió un error inesperado."
                        });

                }


            } catch (error) {

                clearTimeout(timeout);

                Swal.close();

                console.error(
                    "Forgot password error:",
                    error
                );


                Swal.fire({
                    ...SWAL_CONFIG,
                    icon: "error",
                    title: "Error de conexión",
                    text: "No se pudo contactar con el servidor."
                });

            }

        }
    );

}