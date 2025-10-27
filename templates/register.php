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
                        <input type="password" id="password" class="form-control" name="password" placeholder="Enter password"
                            aria-describedby="passwordHelpBlock" required>
                        <div id="passwordHelpBlock" class="form-text">
                            Your password must be 8-20 characters long and contain letters, numbers, and one special character.
                        </div>
                    </div>
                    <button type="submit" class="btn w-100">Register</button>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="?command=login" class="text-decoration-none">Log in here</a></p>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    </body>
</html>