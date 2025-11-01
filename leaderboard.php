<?php 
require('connect-db.php');         // include() 
require('request-db.php');

$leaderboard_entries = getTopPointUsers();   //get all rows in the table

$selected_mode = "allPoints";
$selected_users = "allUsers";
$selected_time = "weekly";

//controls rendering of the table
$dark_row = false;

//making values so we can dynamically select
$game_mode_filter = [];
$game_mode_filter[] = [
    'mode' => 'Easy',
    'text' => "Easy Mode Only" 
];
$game_mode_filter[] = [
    'mode' => 'Medium',
    'text' => "Medium Mode Only" 
];
$game_mode_filter[] = [
    'mode' => 'Hard',
    'text' => "Hard Mode Only" 
];
$game_mode_filter[] = [
    'mode' => 'allPoints',
    'text' => "Total Rank Points" 
];

$friend_filter = [];
$friend_filter[] = [
    'mode' => 'allUsers',
    'text' => "All Users" 
];
$friend_filter[] = [
    'mode' => 'friends',
    'text' => "My Friends" 
];
?>


<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST')  
{
   if (!empty($_POST['refreshBtn']))
   {
    //idea here: filter the entries accordingly
    echo "Mode selected: " . $_POST['modeSelect'];
    echo "Friend users selected: " . $_POST['friendSelect'];
    echo "Time range selected: " . $_POST['timeSelect'];

    $selected_mode = $_POST['modeSelect'];
    $selected_users = $_POST['friendSelect'];
    $selected_time = $_POST['timeSelect'];

    $leaderboard_entries = processFiltering($selected_mode, $selected_users, $selected_time);

    //   addRequests($_POST['requestedDate'], $_POST['roomNo'], $_POST['requestedBy'], 
    //               $_POST['requestDesc'], $_POST['priority_option']);

    //   //read the table values again (refresh)
    //   $list_of_requests = getAllRequests();
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
  <meta name="description" content="Minesweeper leaderboard page">
  <meta name="keywords" content="minesweeper game leaderboard shop database">
  
  <title>Minesweeper Leaderboard</title>
   <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">

    <style>
        tr.table-warning {
            --bs-table-bg: #FFE9B1 !important; /* Light Yellow */
            border-color: #FFE9B1 !important;
        }

        tr.table-danger {
            --bs-table-bg: #FFD788 !important; /* Dark Yellow */
            border-color: #FFD788 !important;
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
            <a class="nav-link disabled" href="shop.html" tabindex="-1" aria-disabled="true">Shop</a>
        </ul>
        <div class="m-5" style="width:68%;"> 
            <h2 class="mb-5"> Leaderboard </h2>
            <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
                <div style="display: flex; flex-direction: row;">
                    <select id="modeSelect" name="modeSelect" class="form-select m-2">
                        <?php foreach ($game_mode_filter as $game_mode): ?>
                            <option value=<?php echo $game_mode['mode'] ?> <?php if ($selected_mode == $game_mode['mode']) echo 'selected'; ?>>
                                <?php echo $game_mode['text'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="friendSelect" name="friendSelect" class="form-select m-2">
                        <?php foreach ($friend_filter as $friend_mode): ?>
                            <option value=<?php echo $friend_mode['mode'] ?> <?php if ($selected_users == $friend_mode['mode']) echo 'selected'; ?>>
                                <?php echo $friend_mode['text'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="timeSelect" name="timeSelect" class="form-select m-2">
                        <option value="weekly" selected>This Week</option>
                        <option value="today">Today</option>
                        <option value="monthly">This Month</option>
                        <option value="allTime">All Time</option>
                    </select>
                    <input class="btn m-2" style="background-color: #FFD788" type="submit" Value="refresh!" name="refreshBtn">
                    </input>
                </div>
            </form>
            

            <div class="row justify-content-center">  
            <table class="table mt-3" style="width:90%">
                <thead style="--bs-table-bg:rgba(249, 215, 143); border-color:rgba(249, 215, 143)">
                <tr>
                    <th width="40%"><b>Username</b></th>
                    <?php if ($selected_mode == "allPoints") {echo "<th width='40%'><b>Total Points</b></th>"; }?>   
                    <?php if ($selected_mode != "allPoints") {echo "<th width='40%'><b>Mode</b></th>"; }?> 
                    <?php if ($selected_mode != "allPoints") {echo "<th width='40%'><b>Finish Time</b></th>"; }?>   
                </tr>
                </thead>

                <?php foreach ($leaderboard_entries as $board_entry): 
                    $row_class = $dark_row ? 'table-danger' : 'table-warning';
                    // Flip the value of $dark_row for the next iteration
                    $dark_row = !$dark_row;
                ?>
                    <tr class="<?php echo $row_class; ?>">
                    <td><?php echo $board_entry['username']; ?></td>
                    <?php if ($selected_mode == "allPoints") { echo "<td>" . $board_entry['totalScore'] . "</td>"; } ?>
                    <?php if ($selected_mode != "allPoints") { echo "<td>" . $board_entry['mode'] . "</td>"; } ?>
                    <?php if ($selected_mode != "allPoints") { echo "<td>" . $board_entry['gameTime'] . "</td>"; } ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>  

        </div>
    </div>


<br/><br/>

<!-- <script src='maintenance-system.js'></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>
</html>