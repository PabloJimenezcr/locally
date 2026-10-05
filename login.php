<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locally</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/loginR.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

    <div class="container-auth" id="container">
        <div class="form-container sign-up">
            <form id="registerForm"
                action="actions/register.php"
                method="POST"
                enctype="multipart/form-data">
                <h2>Create Account</h2>

                <div class="profile-upload">
                    <input
                        type="file"
                        id="profilePhoto"
                        name="photo"
                        accept="image/*" hidden>

                    <label for="profilePhoto" class="profile-preview" aria-label="Upload profile photo">
                        <img id="profilePreview" src="" alt="">
                        <span class="profile-placeholder">
                            <i class="ri-camera-line" aria-hidden="true"></i>
                        </span>
                    </label>

                    <small>Upload profile photo</small>
                </div>

                <div class="input-group">
                    <input name="name" type="text" id="name" placeholder=" ">
                    <label for="name">Name</label>
                </div>

                <div class="input-group">
                    <input name="email" type="email" id="email2" placeholder=" ">
                    <label for="email2">Email</label>
                </div>

                <div class="input-group">
                    <input name="password" type="password" id="pass2" placeholder=" ">
                    <label for="pass2">Password</label>
                    <button class="toggle-pass" type="button" onclick="togglePass(this,'pass2')"
                        aria-label="Show password">
                        <i class="ri-eye-line" aria-hidden="true"></i>
                    </button>
                </div>


                <button type="submit">Sign Up</button>

                <p class="switch-text">
                    ¿Ya tienes cuenta?
                    <button type="button" onclick="toggleAuth()">Inicia sesión</button>

                </p>
            </form>
        </div>


        <div class="form-container sign-in">
            <form id="loginForm"
                action="actions/login.php"
                method="POST">
                <h2>Sign In</h2>
                <div class="input-group">
                    <input name="email" type="email" id="email" placeholder=" ">
                    <label for="email">Email</label>
                </div>

                <div class="input-group">
                    <input name="password" type="password" id="pass" placeholder=" ">
                    <label for="pass">Password</label>
                    <button class="toggle-pass" type="button" onclick="togglePass(this,'pass')"
                        aria-label="Show password">
                        <i class="ri-eye-line" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="forgot-password">

                    <a
                        href="#"
                        id="forgotPasswordBtn">

                        ¿Olvidaste tu contraseña?

                    </a>

                </div>

                <button type="submit">Sign In</button>

                <p class="switch-text">
                    ¿No tienes cuenta?
                    <button type="button" onclick="toggleAuth()">Regístrate</button>
                </p>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h2>Welcome Back</h2>
                    <button class="ghost" type="button" onclick="toggleAuth()">Sign In</button>
                </div>

                <div class="overlay-panel overlay-right">
                    <h2>New Here?</h2>
                    <button class="ghost" type="button" onclick="toggleAuth()">Sign Up</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/login.js"></script>

</body>

</html>