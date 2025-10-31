<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="description" content="Register Screen for Minesweeper">
        <meta name="author" content="Natalia Wunder">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta property="og:title" content="Minesweeper">
        <meta property="og:type" content="website">
        <meta property="og:image" content="">
        <meta property="og:url" content="">
        <meta property="og:description"
        content="Website for CS 4750 hosting a Minesweeper website.">
        <meta property="og:site_name" content="Minesweeper">

        <title>Register for Minesweeper</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

        <link rel="stylesheet" href="styles.css">
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
                            <a class="nav-link active" href="?command=register">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?command=login">Log In</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="text-center">
                <h1 class="display-4 pt-3">Minesweeper</h1>
                <hr>
            </div>

            <div class="text-center" id="jsMessage">
                <?=$message?>
            </div>
        </div>

        <div class="container d-flex justify-content-center align-items-center mt-3">
            <div class="card p-4 shadow" style="width: 600px;">
                <h2 class="text-center mb-4">Register</h2>
                <form action="?command=register" method="post">
                    <div class="form-group mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" name="username" placeholder="Enter username" required>
                        <div id="usernameHelpBlock" class="form-text d-none">
                            Your username must only contain letters, numbers, and underscores.
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" id="email" class="form-control" name="email" placeholder="name@example.com" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Enter password" required>
                        <div id="passwordHelpBlock" class="form-text d-none">
                            Your password must contain ...
                        </div>
                    </div>
                    <button type="submit" class="btn w-100">Register</button>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="?command=login" class="text-decoration-none">Log in here</a></p>
                </div>
            </div>
        </div>

        <script>
            function showUsernameInfo() {
                document.getElementById("usernameHelpBlock").classList.remove("d-none");
            }
          
            function hideUsernameInfo() {
                document.getElementById("usernameHelpBlock").classList.add("d-none");
            }
          
            document.addEventListener("DOMContentLoaded", function () {
                usernameInput = document.getElementById("username");
            
                usernameInput.addEventListener("focus", showUsernameInfo);
                usernameInput.addEventListener("blur", hideUsernameInfo);
            });

            function showPasswordInfo() {
                document.getElementById("passwordHelpBlock").classList.remove("d-none");
            }
          
            function hidePasswordInfo() {
                document.getElementById("passwordHelpBlock").classList.add("d-none");
            }
          
            document.addEventListener("DOMContentLoaded", function () {
                passwordInput = document.getElementById("password");
            
                passwordInput.addEventListener("focus", showPasswordInfo);
                passwordInput.addEventListener("blur", hidePasswordInfo);
            });
        
            document.addEventListener("DOMContentLoaded", function () {
                form = document.querySelector("form");
                username = document.getElementById("username");
                email = document.getElementById("email");
                password = document.getElementById("password");
                jsMessage = document.getElementById("jsMessage");
            
                form.addEventListener("submit", function (event) {
                    jsMessage.innerHTML = "";
                
                    let isValid = true;
                    let errorMessage = "";
                
                    usernameRegex = /^\w+$/;
                    if (!usernameRegex.test(username.value)) {
                        errorMessage = "Invalid username format. Please try again.";
                        isValid = false;
                    }
                
                    emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email.value)) {
                        errorMessage = "Invalid email format. Please try again.";
                        isValid = false;
                    }
                
                    passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>])[\S]{8,20}$/;
                    if (!passwordRegex.test(password.value)) {
                        errorMessage = "Invalid password format. Please try again.";
                        isValid = false;
                    }
                
                    // prevent submission if it fails
                    if (!isValid) {
                        event.preventDefault();
                        showError(errorMessage);
                    }
                });
                
                function showError(message) {
                    alert = document.createElement("p");
                    alert.classList.add("alert", "alert-danger");
                    alert.innerText = message;
                    jsMessage.appendChild(alert);
                }
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
    </body>
</html>