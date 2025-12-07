<?php
require('connect-db.php');
require('request-db.php');

session_start();


$mode = null;
$gamemodeInfo = null;
$userStats = null;

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


?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['easyBtn'])) {
        $mode = "Easy";
        $gamemodeInfo = getGamemodeInfo($mode);
        addNewGame($_SESSION["username"], $gamemodeInfo);
    } else if (!empty($_POST['mediumBtn'])) {
        $mode = "Medium";
        $gamemodeInfo = getGamemodeInfo($mode);
        addNewGame($_SESSION["username"], $gamemodeInfo);
    } else if (!empty($_POST['hardBtn'])) {
        $mode = "Hard";
        $gamemodeInfo = getGamemodeInfo($mode);
        addNewGame($_SESSION["username"], $gamemodeInfo);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Megan Natalia Nicole Sarina">
    <meta name="description" content="Minesweeper create a game page">
    <meta name="keywords" content="minesweeper game database">

    <title>Minesweeper Create Game</title>
    <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <style>
        .body {
            overflow: hidden;
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

        .profile-dropdown {
            background-color: rgba(252, 245, 217);
            width: 100px;
            margin-right: 80px;
        }

        .dropdown-menu {
            margin-right: 50px;
        }

        .navbar {
            position: relative !important;
        }

        .vertical-nav {
            position: relative !important;
            top: 0px !important;
        }

        #page-title {
            text-align: center;
            font-family: 'Inder', sans-serif;
            font-weight: bolder;
            font-size: 36px;
            background-color: #ffc562;
            padding: 20px;
            border-radius: 15px;
        }

        #playBtn {
            font-family: 'Inder', sans-serif;
        }

        #playBtn:hover {
            background-color: #ff0000ff;

        }
    </style>

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

    <div class="nav flex-row">
        <ul class="vertical-nav">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
            <!-- For tabs the user doesn't have access to, while logged out, do we want to hide 
            or disable them? -->
            <a class="nav-link" href="shop.php" tabindex="-1">Shop</a>
        </ul>
        <div class="m-5" style="width:68%;">
            <h2 class="mb-5" id="page-title"> Create New Game </h2>
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" class="container text-center mt-4">
                <div class="row mb-2">
                    <div class="col-md-6 mb-2">
                        <input class="btn btn-lg w-75" id="playBtn"
                            style="background-color: #FFD788; padding: 18px 40px; font-size: 1.5rem; font-weight: bold;"
                            type="submit" Value="Easy" name="easyBtn">
                        <p class="mt-2">
                            Height: 10 <br>
                            Width: 10 <br>
                            # of Mines: 10 <br>
                            Points: 5 </p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input class="btn btn-lg w-75" id="playBtn"
                            style="background-color: #FFD788; padding: 18px 40px; font-size: 1.5rem; font-weight: bold;"
                            type="submit" Value="Medium" name="mediumBtn">
                        <p class="mt-2">
                            Height: 15 <br>
                            Width: 15 <br>
                            # of Mines: 40 <br>
                            Points: 30 </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <input class="btn btn-lg" id="playBtn"
                            style="background-color: #FFD788; padding: 18px 125px; font-size: 1.5rem; font-weight: bold;"
                            type="submit" Value="Hard" name="hardBtn">
                        <p class="mt-2">
                            Height: 17 <br>
                            Width: 17 <br>
                            # of Mines: 55 <br>
                            Points: 80 </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>