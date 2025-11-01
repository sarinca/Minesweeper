<?php
require('connect-db.php');    
require('request-db.php');

$gamemodeInfo = null;

?>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (!empty($_POST['easyBtn'])){
        $mode = "Easy";
    } else if (!empty($_POST['mediumBtn'])) {
        $mode = "Medium";
    } else if (!empty($_POST['hardBtn'])){
        $mode = "Hard";
    }
    $gamemodeInfo = getGamemodeInfo($mode);
    $row = $gamemodeInfo->fetch_assoc();

    $height = $row['height'];
    $width = $row['width'];
    $numBombs = $row['numBombs'];
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
            <a class="nav-link disabled" href="shop.html" tabindex="-1" aria-disabled="true">Shop</a>
        </ul>
        <div class="m-5" style="width:68%;"> 
            <h2 class="mb-5"> Create New Game </h2>
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" class="container text-center mt-4">
                <div class="row mb-2">
                    <div class="col-md-6 mb-2">
                        <input class="btn btn-lg w-75" style="background-color: #FFD788; padding: 18px 40px; font-size: 1.5rem; font-weight: bold;" type="submit" Value="Easy" name="easyBtn">
                        <p class="mt-2"> 
                            Height: 10 <br>
                            Width: 10 <br>
                            # of Mines: 10 <br>
                            Points: 5 </p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input class="btn btn-lg w-75" style="background-color: #FFD788; padding: 18px 40px; font-size: 1.5rem; font-weight: bold;" type="submit" Value="Medium" name="mediumBtn">
                        <p class="mt-2"> 
                            Height: 15 <br>
                            Width: 15 <br>
                            # of Mines: 40 <br>
                            Points: 30 </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <input class="btn btn-lg" style="background-color: #FFD788; padding: 18px 125px; font-size: 1.5rem; font-weight: bold;" type="submit" Value="Hard" name="hardBtn">
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


</body>
</html>
