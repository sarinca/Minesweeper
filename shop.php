<?php
require('connect-db.php');         // include() 
require('request-db.php');

session_start();

// log out functionality (for log out dropdown href, do pagename.php?action=logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php"); //can change to login if we want
    exit;
}

set_error_handler(function () {
    /* Intentionally ignore all errors during this block */
});

$user_loggedIn = false;
$userStats = null;


if ($_SESSION["username"] == NULL) {
    echo "Session not established yet";
    $user_points = 999;
} else {
    $user_loggedIn = true;
    $user_points = getUserPoints($_SESSION["username"])[0];
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userStats = getUserStats($user_id);
    }
}

restore_error_handler();

$shop_items = getShopItems();   //get all rows in the table
?>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['buyBtn'])) {

        //CHECK IF THIS IS A VALID PURCHASE
        if ($user_points >= $_POST['price']) {

            //TO-DO: turn this into a temporary success pop-up with information
            // echo "Bought " . $_POST['name'] . " for: " . $_POST['price'] . " points";

            //this updates the user points AND adds the item to the user's account 
            handlePurchase($_SESSION["username"], $_POST['name'], $_POST['price']);
            $user_points = $user_points - $_POST['price'];
        } else {
            echo "ERROR: You don't have enough points for this item, sorry";
        }
    }
    if (!empty($_POST['extraPointsBtn'])) {
        //this is the shortcut for demo 
        addPointsForTesting($_SESSION["username"]);
        $user_points = $user_points + 100;

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
    <meta name="description" content="Minesweeper shop page">
    <meta name="keywords" content="minesweeper game shop database">

    <title>Minesweeper Shop</title>
    <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inder&display=swap');

        .btn-success {
            --bs-btn-bg: #76b881;
            --bs-btn-border-color: #bae0bd;
            --bs-btn-disabled-border-color: #c9cfc9;
            --bs-btn-disabled-bg: #c9cfc9;
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

        #page-title{
            text-align: center;
            font-family: 'Inder', sans-serif;
            font-weight: bolder;
            font-size: 36px;
            background-color: #ffc562;
            padding: 20px;
            border-radius: 15px;
        }

        #available-points {
            font-family: 'Inder', sans-serif;
            text-align: right; 
            font-size: 12px;

            margin: auto;
            font-size: 12px;
            background-color: #dce8f2ff;
            padding: 25px;
            width: fit-content;
            margin-bottom: 15px;
            margin-right: 75px;
            border-radius: 15px;
        }

    </style>
</head>

<body>
    <!-- Top Navigation Bar [ Minesweeper Title, User Profile Button ]-->
    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a href="index.php" class="navbar-parent">Minesweeper</a>
            <?php if ($user_loggedIn && $userStats): ?>
                <div class="d-flex align-items-center">
                    <img src="<?php echo !empty($userStats['profilePicture_path'])
                        ? htmlspecialchars($userStats['profilePicture_path'])
                        : 'https://i.pinimg.com/custom_covers/222x/85498161615209203_1636332751.jpg'; ?>"
                        alt="Profile Picture" id="pfp" class="rounded-circle me-2" width="40" height="40">

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
                            <li><a class="dropdown-item" href="shop.php?action=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="nav flex-row">
        <ul class="vertical-nav">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link" href="create_game.php">Play</a>
            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
            <a class="nav-link active" href="shop.php" tabindex="-1">Shop</a>
        </ul>
        <div class="m-5" style="width:68%;">
            <h2 class="mb-4" id="page-title"> Shop </h2>
            <h4 id = "available-points">Available points: <?php echo $user_points ?></h4>
            <div class="justify-content-center" style="flex-direction:row; display: flex; flex-wrap: wrap;">
                <?php foreach ($shop_items as $item):
                    $itemId = str_replace(' ', '', $item['name']) . "card";
                    $buttonText = "Buy for " . $item['price'] . " points";
                    $buttonClass = "btn btn-success mb-4";
                    if ($user_points < $item['price']) {
                        $buttonText = "not enough points";
                        $buttonClass = $buttonClass . " disabled";
                    } ?>
                    <div style="width:30%; text-align: center;">
                        <div class="shop-card" id=<?php echo $itemId ?> name=<?php echo $itemId ?>>
                            <img src=<?php echo $item['picPath'] ?> alt="Item Picture" class="rounded-circle mb-4"
                                width="100" height="100">
                            <div> <?php echo $item['description'] ?> </div>
                            <div class="warning-text">{valid for one use only}</div>
                        </div>
                        <div class="mb-1 mt-3"> <?php echo $item['name'] ?> </div>
                        <form method="post" action="shop.php">
                            <input type="hidden" name="name" value="<?php echo $item['name']; ?>" />
                            <input type="submit" name="buyBtn" class="<?php echo $buttonClass ?>"
                                value="<?php echo $buttonText ?>"></input>
                            <input type="hidden" name="price" value="<?php echo $item['price']; ?>" />
                        </form>
                    </div>

                <?php endforeach; ?>
            </div>
            <form method="post" action="shop.php">
                <input type="submit" name="extraPointsBtn" class="btn btn-primary" style="margin-top: 325px"
                    value="Shh.. add 100 points to account"></input>
            </form>
        </div>
    </div>


    <br /><br />

    <!-- <script src='maintenance-system.js'></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>