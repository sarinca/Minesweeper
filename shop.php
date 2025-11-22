<?php 
require('connect-db.php');         // include() 
require('request-db.php');

session_start();
echo $_SESSION["username"];
echo " ";
echo $_SESSION["email"];

$shop_items = getShopItems();   //get all rows in the table
$user_points = 95;        //THIS IS A DEFAULT VALUE, NEED TO CHANGE LATER W/DB REQ
?>


<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST')  
{
   if (!empty($_POST['buyBtn']))
   {
    //idea here: filter the entries accordingly
    echo "Bought ". $_POST['name'] . " for: " . $_POST['price'] . " points";

    //UPDATE THE USER POINTS IN DB
    //ADD THIS ITEM TO THE USER's ACCOUNT IN DB

    $user_points -= $_POST['price'];

    //RERENDER THE BUTTONS AND TEXT
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
        .btn-success {
            --bs-btn-bg: #76b881;
            --bs-btn-border-color: #bae0bd;
            --bs-btn-disabled-border-color: #c9cfc9;
            --bs-btn-disabled-bg: #c9cfc9;
        }
    </style>
</head>

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
            <a class="nav-link" href="shop.php" tabindex="-1">Shop</a>
        </ul>
        <div class="m-5" style="width:68%;"> 
            <h2 class="mb-4"> Shop </h2>
            <h4 style="text-align: right; margin-bottom: 40px;">Available points: <?php echo $user_points ?></h4>
                <div class="justify-content-center" style="flex-direction:row; display: flex; flex-wrap: wrap;">  
                    <?php foreach ($shop_items as $item): 
                        $itemId = str_replace(' ', '', $item['name']) . "card"; 
                        $buttonText = "Buy for " . $item['price'] . " points";
                        $buttonClass = "btn btn-success mb-4";
                        if ($user_points < $item['price']) { $buttonText = "not enough points"; $buttonClass = $buttonClass . " disabled";}?>
                        <div style="width:30%; text-align: center;">
                            <div class="shop-card" id= <?php echo $itemId ?> name=<?php echo $itemId ?>>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Default_pfp.svg/2048px-Default_pfp.svg.png"
                        alt="Profile Picture" class="rounded-circle mb-4" width="100" height="100">
                                <div> <?php echo $item['description'] ?> </div>
                                <div class="warning-text">{valid for one use only}</div>
                            </div>
                            <div class="mb-1 mt-3"> <?php echo $item['name'] ?> </div>
                            <form method="post" action="shop.php">
                                <input type="hidden" name="name" value="<?php  echo $item['name']; ?>"/>
                                <input type="submit" name="buyBtn" class="<?php echo $buttonClass ?>" value="<?php echo $buttonText ?>"></input>
                                <input type="hidden" name="price" value="<?php  echo $item['price']; ?>"/>
                            </form>
                        </div>

                    <?php endforeach; ?>
                </div>              
        </div>
    </div>


<br/><br/>

<!-- <script src='maintenance-system.js'></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>
