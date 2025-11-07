<?php 
session_start();
include_once('connect-db.php');         
include_once('request-db.php');
$message = "";
?>

<?php 
    // source for cookie log in: https://www.youtube.com/watch?v=kmaUSZXfbxg
    // check if form submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            isset($_POST["username"]) && isset($_POST["password"]) &&
            !empty($_POST["username"]) && !empty($_POST["password"])
        ) {
        
        $results = login($_POST["username"]);

        if (empty($results)) {
            // user not found
            $message = "<p class='alert alert-danger'>User not found. Please register an account.</p>";
            //include("login.php");
            // return;
        } else {
            // check if the password is correct
            $hashed_password = $results["password"];
            $correct = password_verify($_POST["password"], $hashed_password);
            if ($correct) {
                // success!
                $_SESSION["user_id"] = $results['id'];
                $_SESSION["username"] = $_POST["username"];
                $_SESSION["email"] = $results['email'];

                if (!empty($_POST["rememberMe"])) {
                    setcookie("username", $_POST["username"], time() + (3600 * 24 * 7));
                    setcookie("password", $_POST["password"], time() + (3600 * 24 * 7));
                    setcookie("userlogin", $_POST["rememberMe"], time() + (3600 * 24 * 7));
                } else {
                    // remove cookie if unchecked
                    setcookie("username", $_POST["username"], 30);
                    setcookie("password", $_POST["password"], 30);
                }

                // redirect to profile screen (set to shop for now to test)
                header("Location: shop.php");
                exit;
                // return;
            } else {
                // incorrect password
                $message = "<p class='alert alert-danger'>Incorrect password. Please try again.</p>";
                // $this->showLogin($message, "");
                //include("login.php");
                // return;
            }
        }
        } else {
            // form submitted with missing fields
            $message = "<p class='alert alert-danger'>Username or password missing.</p>";
            // $this->showLogin($message, "");
            //include("login.php");
            // return;
        }
    }

    $savedUsername = "";
    if (isset($_COOKIE["remembered_user"])) {
        $savedUsername = $_COOKIE["remembered_user"];
    }

    // $this->showLogin("", $savedUsername);
    //include("login.php");
?>

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
        <meta property="og:image" content="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg">
        <meta property="og:url" content="">
        <meta property="og:description"
        content="Website for CS 4750 hosting a Minesweeper website.">
        <meta property="og:site_name" content="Minesweeper">

        <title>Login to Minesweeper</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <!-- ../styles.css -->
        <link rel="stylesheet" href="styles.css">
    </head>

    <body>
        <nav class="navbar navbar-expand-md px-3 d-none d-md-block">
            <div class="container-fluid">
                <a class="navbar-brand navbar-parent" href="index.html">Minesweeper</a>
            </div>
        </nav>

        <!-- for accessibility on mobile, i made it not show using d-none and d-md-block combo with d-md-none shaow-sm -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-3 d-none d-md-block"> 
                    <nav class="nav flex-column">
                        <ul class="vertical-nav">
                            <a class="nav-link" href="index.html">Home</a>
                            <a class="nav-link active" href="login.php">Login</a>
                            <a class="nav-link" href="?command=play">Play</a>
                            <!-- we need a leaderboard (log vs not logged in) -->
                            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
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
                                    <a class="nav-link" href="index.html">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="login.php">Login</a>
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
                        <h1 class="display-4 pt-5">Login</h1>
                        <hr>

                        <!-- this shows error messages but needs PHP configuration -->
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
                                    <input type="email" id="resetEmail" class="form-control rounded-pill" placeholder="example@email.com">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn loginbtn rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                    <button id="sendResetBtn" type="button" class="btn loginbtn rounded-pill">Send Reset Link</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container d-flex justify-content-center align-items-center mt-5">
                        <div class="card bg-transparent border-0 p-4" style="width: 900px; height: 375px;">
                            <form method="post">
                                <div class="mb-3 form-group">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control rounded-pill" style="background-color: #fbe9af; border-color: #fbe9af;" id="username" name="username" 
                                        placeholder="Username" required 
                                        value="<?php 
                                            if (isset($_COOKIE['username'])) { 
                                                echo $_COOKIE['username']; 
                                            }
                                        ?>">
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control rounded-pill" style="background-color: #fbe9af; border-color: #fbe9af;" id="password" name="password" 
                                        placeholder="Password" required value="<?php 
                                        if (isset($_COOKIE['password'])) { 
                                            echo $_COOKIE['password']; 
                                        }
                                    ?>">
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" style="border-color: #fbe9af;" type="checkbox" id="rememberMe" name="rememberMe" value="
                                            <?php 
                                                if (isset($_COOKIE['userlogin'])) { 
                                                    echo "checked"; 
                                                } 
                                            ?>">
                                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                                    </div>
                                    <a href="#" class="text-decoration-none">Forgot Password?</a>
                                </div>
                                <button type="submit" style="background-color: #fbe9af; border-color: #fbe9af;" class="btn loginbtn rounded-pill w-100">Login</button>
                            </form>
                            <div class="text-center mt-3">
                                <p>Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"></script>
    </body>
</html>