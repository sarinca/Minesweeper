<?php 
require('connect-db.php');         // include() 
require('request-db.php');

$all_user_points = getTopPointUsers();   //get all rows in the table
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">    
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Megan Natalia Nicole Sarina">
  <meta name="description" content="Minesweeper leaderboard page">
  <meta name="keywords" content="minesweeper game leaderboard shop database">
  
  <title>Minesweeper Leaderboard</title>
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
            <h2 class="mb-5"> Leaderboard </h2>

            <div style="display: flex; flex-direction: row;">
                <select id="modeSelect" class="form-select m-2">
                    <option value="allPoints" selected>Total Rank Points</option>
                    <option value="easy">Easy Mode Only</option>
                    <option value="med">Medium Mode Only</option>
                    <option value="hard">Hard Mode Only</option>
                </select>
                <select id="friendSelect" class="form-select m-2">
                    <option value="allUsers" selected>All Users</option>
                    <option value="friends">My Friends</option>
                </select>
                <select id="timeSelect" class="form-select m-2">
                    <option value="weekly" selected>This Week</option>
                    <option value="today">Today</option>
                    <option value="monthly">This Month</option>
                    <option value="allTime">All Time</option>
                </select>
            </div>

            <div class="row justify-content-center">  
            <table class="w3-table w3-bordered w3-card-4 center" style="width:90%%">
                <thead>
                <tr>
                    <th width="40%"><b>Username</b></th>
                    <th width="40%"><b>Total Points</b></th>        
                </tr>
                </thead>

                <?php foreach ($all_user_points as  $board_entry): ?>

                    <tr>
                    <td><?php echo $board_entry['username']; ?></td>
                    <td><?php echo $board_entry['totalScore']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>  

        </div>
    </div>

<div class="container">
  <div class="row g-3 mt-2">
    <div class="col">
      <h2>Leaderboard</h2>
    </div>  
  </div>
</div>


<hr/>
<div class="container">
<div class="row justify-content-center">  
<table class="w3-table w3-bordered w3-card-4 center" style="width:100%">
  <thead>
  <tr style="background-color:#B0B0B0">
    <th width="40%"><b>Username</b></th>
    <th width="40%"><b>Total Points</b></th>        
  </tr>
  </thead>

  <?php foreach ($all_user_points as  $board_entry): ?>

    <tr>
      <td><?php echo $board_entry['username']; ?></td>
      <td><?php echo $board_entry['totalScore']; ?></td>
    </tr>
  <?php endforeach; ?>

</table>
</div>   


<br/><br/>

<!-- <script src='maintenance-system.js'></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>