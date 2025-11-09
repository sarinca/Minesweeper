<?php 
session_start();
include_once('connect-db.php');         
include_once('request-db.php');
$message = "";
?>

<?php
    // check if form submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
                isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) &&
                !empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"])
            ) {

                // check that username looks right using regular expressions
                if (!preg_match("/^\w+$/", $_POST["username"])) {
                    $message = "<p class='alert alert-danger'>Invalid username format. Please try again.</p>";
                    //include("register.php");
                    //return;
                }

                // check that email looks right using regular expressions
                else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $message = "<p class='alert alert-danger'>Invalid email format. Please try again.</p>";
                    //include("register.php");
                    //return;
                }

                // check that password format is correct
                // minimum eight characters, at least one uppercase letter, one lowercase letter, one number, one special character
                else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $_POST["password"])) {
                    $message = "<p class='alert alert-danger'>Invalid password format. Please try again.</p>";
                    //include("register.php");
                    //return;
                }
                else {
                    // check if email and username is already registered
                    $result = check_registration($_POST["email"], $_POST["username"]);
                    
                    if (empty($result)) {
                        // create the user account
                        $result = register($_POST["email"], $_POST["username"], password_hash($_POST["password"], PASSWORD_DEFAULT));

                        // redirect to login screen
                        header("Location: login.php");
                        exit;
                        //return;
                    } else {
                        // email or username already registered
                        $message = "<p class='alert alert-danger'>Account already registered. Try again or log in.</p>";
                        //include("register.php");
                        //return;
                    }
                }
        }   
        else {
            // form is submitted but required fields are missing
            $message = "<p class='alert alert-danger'>Username, email, or password missing.</p>";
            //include("register.php");
            //return;
        }
    }

    // if the page is accessed without form submission, no error message
    //include("register.php");
?>

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

            <!-- href="../styles.css" -->
        <link rel="stylesheet" href="styles.css">
    </head>

    <body data-new-gr-c-s-check-loaded="14.1093.0" data-gr-ext-installed="">
        <nav class="navbar navbar-expand-md px-3 d-none d-md-block">
            <div class="container-fluid">
                <a class="navbar-brand navbar-parent" href="index.html">Minesweeper</a>
            </div>
        </nav>
    
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-3 d-none d-md-block"> 
                    <nav class="nav flex-column">
                        <ul class="vertical-nav">
                            <a class="nav-link" href="index.html">Home</a>
                            <a class="nav-link" href="login.php">Login</a>
                            <a class="nav-link" href="?command=play">Play</a>
                            <a class="nav-link" href="leaderboard,php">Leaderboard</a>
                        </ul>
                    </nav>
                </div>

                <!-- visible only on small screen -->
                <nav class="navbar navbar-expand-md px-3 d-md-none shadow-sm">
                    <div class="container-fluid">
                        <a class="navbar-brand fw-bold">Minesweeper</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="login.php">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="?command=play">Play</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="leaderboard.php">Leaderboard</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="col-12 col-md-8">
                    <div class="container">
                        <h1 class="display-4 pt-5">Register</h1>
                        <hr>

                        <!-- this shows error messages but needs PHP configuration -->
                        <div class="text-center">
                            <?=$message?>
                        </div>
                    </div>
                    <div class="container d-flex justify-content-center align-items-center mt-4">
                        <div class="card bg-transparent border-0 p-4" style="width: 900px; height: 375px;">
                            <form id="register" method="post">
                                <div class="form-group mb-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" id="username" class="form-control rounded-pill" name="username" placeholder="Enter username" required>
                                    <div id="usernameHelpBlock" class="form-text d-none">
                                        Your username must only contain letters, numbers, and underscores.
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" id="email" class="form-control rounded-pill" name="email" placeholder="name@example.com" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" class="form-control rounded-pill" name="password" placeholder="Enter password" required>
                                    <div id="passwordHelpBlock" class="form-text d-none">
                                        Your password must contain ...
                                    </div>
                                </div>
                                <button type="submit" class="btn rounded-pill loginbtn w-100" style="background-color: #fbe9af; border-color: #fbe9af;">Register</button>
                            </form>
                            <div class="text-center mt-3">
                                <p>Already have an account? <a href="login.php" class="text-decoration-none">Log in here</a></p>
                            </div>
                        </div>
                    </div>
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
                
                    passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
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
