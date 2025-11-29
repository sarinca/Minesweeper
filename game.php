<?php
require('connect-db.php');    
require('request-db.php');

$gameId = $_GET['gameId'];

$gameInfo = getGameInfo($gameId);
$mode = $gameInfo['mode'];

$gamemodeInfo = getGamemodeInfo($mode);

$height = $gamemodeInfo['height'];
$width = $gamemodeInfo['width'];
echo "game.php loaded";
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">    
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Megan Natalia Nicole Sarina">
  <meta name="description" content="Minesweeper game page">
  <meta name="keywords" content="minesweeper game database">
  
  <title>Minesweeper Game</title>
  <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <style>
        #board {
            display: grid;
            grid-template-columns: repeat(<?php echo $width; ?>, 30px);
            grid-template-rows: repeat(<?php echo $height; ?>, 30px);
        }
        .cell {
            width: 30px;
            height: 30px;
            background-color: #ccc;
            border: 1px solid #999;
            cursor: pointer;
        }
        .navbar {
            position: relative !important;
        }
        .vertical-nav {
            position: relative !important;
            top: 0px !important;
        }
    </style>



<body>
     <!-- Top Navigation Bar [ Minesweeper Title, User Profile Button ]-->
    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a class="navbar-parent">Minesweeper</a>
            <div class="d-flex align-items-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Default_pfp.svg/2048px-Default_pfp.svg.png"
                    alt="Profile Picture" id="pfp" class="rounded-circle me-2" width="40" height="40">

                <div class="profile-dropdown">
                    <!-- Dropdown toggle button (always shows username) -->
                    <button class="btn dropdown-toggle" type="button" id="userDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        defaultUser
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                        <li> <hr class="dropdown-divider"> </li>
                        <li><a class="dropdown-item" href="login_page.html">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="nav flex-row">
        <ul class="vertical-nav">
            <a class="nav-link" href="index.html">Home</a>
            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
            <!-- For tabs the user doesn't have access to, while logged out, do we want to hide 
            or disable them? -->
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Shop</a>
        </ul>
    </div>


</body>
</html>