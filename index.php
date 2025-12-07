<?php
require('connect-db.php');
require('request-db.php');

session_start();

//log out functionality
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$user_loggedIn = false;
$userStats = null;

set_error_handler(function () {
    /* Intentionally ignore all errors during this block */
});

if ($_SESSION["username"] == NULL) {
    // echo "Session not established yet";
} else {
    // echo $_SESSION["username"];
    // echo " ";
    // echo $_SESSION["email"];
    $user_loggedIn = true;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userStats = getUserStats($user_id);
    }
}

restore_error_handler();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minesweeper</title>

    <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inder&display=swap');

        body {
            overflow: hidden;
        }
        .btn-primary {
            --bs-btn-bg: #7ba1f5;
            --bs-btn-border-color: #82a1e5;
        }

        .navbar {
            position: relative !important;
        }

        .vertical-nav {
            position: relative !important;
            top: 0px !important;
        }

        .profile-dropdown {
            background-color: rgba(252, 245, 217);
            width: 100px;
            margin-right: 80px;
        }

        .dropdown-menu {
            margin-right: 50px;
        }
        .vertical-nav {
            position: fixed;
            left: 0;
            top: 70px;
            width: 200px;
            height: calc(100vh - 70px);
            background-color: rgba(252, 245, 217);
            padding: 2rem 0;
            z-index: 999;
        }

        .vertical-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .vertical-nav .nav-link {
            color: #000000ff;
            padding: 1rem 1.5rem;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

        .vertical-nav .nav-link:hover {
            background-color: #fbe9af;
            border-left-color: #ffc562;
        }

        .instructions {
            height: 400px;
            
        }

        #howToPlay{
            text-align: center;
            font-family: 'Inder', sans-serif;
            font-weight: bolder;
            font-size: 36px;
            background-color: #ffc562;
            padding: 20px;
            border-radius: 15px;
        }

        #makeGameBtn {
            font-family: 'Inder', sans-serif;
            background-color: #ffc562;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar [ Minesweeper Title, User Profile Button ]-->
    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a class="navbar-parent">Minesweeper</a>
            <?php if ($user_loggedIn && $userStats): ?>
                <div class="d-flex align-items-center">
                    <img src="<?php echo !empty($userStats['profilePicture_path'])
                        ? htmlspecialchars($userStats['profilePicture_path'])
                        : 'https://i.pinimg.com/custom_covers/222x/85498161615209203_1636332751.jpg'; ?>" id="pfp"
                        class="rounded-circle me-2" width="40" height="40">

                    <div class="profile-dropdown">
                        <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <?php echo htmlspecialchars($userStats['username']); ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="index.php?action=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <nav class="nav flex-row">
        <ul class="vertical-nav">
            <a class="nav-link active" href="index.php">Home</a>
            <?php if ($user_loggedIn == false) {
                echo "<a class='nav-link' href='login.php'>Login</a>";
            } ?>
            <?php if ($user_loggedIn == false) {
                echo "<a class='nav-link' href='register.php'>Register</a>";
            } ?>
            <?php if ($user_loggedIn == true) {
                echo "<a class='nav-link' href='leaderboard.php'>Leaderboard</a>";
            } ?>
            <?php if ($user_loggedIn == true) {
                echo "<a class='nav-link' href='shop.php' tabindex='-1'>Shop</a>";
            } ?>
        </ul>
        <div class="m-5" style="width:68%;">
            <h2 class="mb-4" id="howToPlay"> How to Play!</h2>
            <div style="display:flex; justify-content: center;">
                <img class="instructions" src="./images/how_to_play_3.jpg">
            </div>
            <?php
            if ($user_loggedIn == true) {
                //display a button that says play game
                //NOTE: we need to change this link so that we can link it to the game page
                echo '<div style = "display:flex; justify-content: center;">
                <a class="btn loginbtn rounded-pill mt-3" id="makeGameBtn" href="create_game.php">Start a game!</a>
                </div>';
            } else {
                //log in to play!
                echo '<div style = "display:flex; justify-content: center;">
                <a class="btn loginbtn rounded-pill mt-3" href="login.php">Login to play a game!</a>
                </div>';
            }
            ?>


        </div>
    </nav>



    <!-- scripts, if needed-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>