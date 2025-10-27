<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="description" content="Login screen for Minesweeper">
        <meta name="author" content="Natalia Wunder">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="Minesweeper">
        <meta property="og:type" content="website">
        <meta property="og:image" content="">
        <meta property="og:url" content="">
        <meta property="og:description"
        content="Website for CS 4750 hosting a Minesweeper website.">
        <meta property="og:site_name" content="Minesweeper">

        <title>Login to Minesweeper</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

        <link rel="stylesheet" href="styles/main.css">
        <script src="https://cdn.jsdelivr.net/npm/less"></script>
    </head>

    <body data-new-gr-c-s-check-loaded="14.1093.0" data-gr-ext-installed="">
        <nav class="navbar navbar-expand-md">
            <div class="container-fluid">
                <a class="navbar-brand" href="?command=home">Minesweeper</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="?command=home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?command=register">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="?command=login">Log In</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container text-center">
            <h1 class="display-4 pt-3">Minesweeper</h1>
            <hr>

            <div class="text-center">
                <?=$message?>
            </div>
        </div>

        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordLabel">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body form-group">
                        <div id="jsMessage" class="mb-2 text-center"></div>
                        <label for="resetEmail" class="form-label">Enter your email address:</label>
                        <input type="email" id="resetEmail" class="form-control" placeholder="example@email.com">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                        <button id="sendResetBtn" type="button" class="btn">Send Reset Link</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="container d-flex justify-content-center align-items-center mt-3">
            <div class="card p-4 shadow" style="width: 600px;">
                <h2 class="text-center mb-4">Login</h2>
                <form action="?command=login" method="post">
                    <div class="mb-3 form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                            placeholder="Enter your username" required 
                            value="<?php 
                                if (isset($_COOKIE['username'])) { 
                                    echo $_COOKIE['username']; 
                                }
                            ?>">
                    </div>
                    <div class="mb-3 form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                            placeholder="Enter your password" required value="<?php 
                            if (isset($_COOKIE['password'])) { 
                                echo $_COOKIE['password']; 
                            } 
                        ?>">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe" value="
                                <?php 
                                    if (isset($_COOKIE['userlogin'])) { 
                                        echo "checked"; 
                                    } 
                                ?>">
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>
                        <a href="#" class="text-decoration-none">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="?command=register" class="text-decoration-none">Register here</a></p>
                </div>
            </div>
        </div>

        <div class="container">
            <footer class="py-3 my-4">
                <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                    <li class="nav-item"><a href="?command=home" class="nav-link px-2 text-muted">Home</a></li>
                    <li class="nav-item"><a href="?command=register" class="nav-link px-2 text-muted">Register</a></li>
                    <li class="nav-item"><a href="?command=login" class="nav-link px-2 text-muted">Log In</a></li>
                </ul>
                <p class="text-center text-muted">© 2025 Minesweeper</p>
                <p class="text-center text-muted">This website is a part of CS 4750 as a class project.</p>
            </footer>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                forgotPasswordLink = document.querySelector('a[href="#"]');
                resetEmailInput = document.getElementById("resetEmail");
                sendResetBtn = document.getElementById("sendResetBtn");
                jsMessage = document.getElementById("jsMessage");
              
                // show pop up on click
                forgotPasswordLink.addEventListener("click", function (e) {
                    resetEmailInput.value = "";
                    jsMessage.querySelectorAll(".alert").forEach(element => element.remove());
                    e.preventDefault();
                    modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                    modal.show();
                });
              
                // not implemented yet
                sendResetBtn.addEventListener("click", function () {
                    jsMessage.querySelectorAll(".alert").forEach(element => element.remove());
                    email = resetEmailInput.value.trim();
                    emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        showError("Please enter a valid email address.");
                    }
                    else {
                        showError("This functionality is not yet finished.");
                    }
                });

                document.getElementById('forgotPasswordModal').addEventListener('hidden.bs.modal', function () {
                    resetEmailInput.value = "";
                    jsMessage.querySelectorAll(".alert").forEach(element => element.remove());
                });
            });
              
            function showError(message) {
                alert = document.createElement("p");
                alert.classList.add("alert", "alert-danger");
                alert.innerText = message;
                jsMessage.appendChild(alert);
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    </body>
</html>