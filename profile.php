<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('connect-db.php');
require('request-db.php');

$user_id = 1; // NOTE: I'LL GET THIS FROM NATALIA LATER? WHATEVER ID THE USER LOGS IN WITH
$user_stats = getUserStats($user_id);
$games_played = getGamesPlayed($user_id);
$friends_list = getUserFriends($user_id);
$game_history = getGameHistory($user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minesweeper</title>

    <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
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
    <!-- </nav>
        <nav class="nav flex-column">
        <ul class="vertical-nav">
            <a class="nav-link" href="home.html">Home</a>
            <a class="nav-link" href="leaderboard.html">Leaderboard</a>
             For tabs the user doesn't have access to, while logged out, do we want to hide 
            or disable them? -->
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Shop</a>
        </ul>
    </nav> 
    
    <div class=usernameDisplay id=usernameDisplay>
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Default_pfp.svg/2048px-Default_pfp.svg.png"
            alt="Profile Picture" id="pfp" class="rounded-circle me-2" width="40" height="40">
        <span id="username"> <?php if($user_stats != null) echo ($user_stats['username']); ?></span>
        <button type="button" class="btn btn-light">Edit</button>
    </div>

    <!-- User Stats Display -->
    <div class=statsDisplay id=statsDisplay>
        <div class=statsTopRow>
            <span id=rank>

            </span>
            <!-- will change this to be high score instead, messed up my SQL but want to make sure we still pulling -->
            <span id=highestScore>Total Score: <?php if($user_stats != null) echo ($user_stats['totalScore']); ?></span>
        </div>
        <div class=statsBottomRow>
            <span id=gamesPlayed>Games Played: 
                <?php echo intval($games_played['games_played'] ?? 0); ?>
        </span>
        <span id=fastestTime>Fastest Time:
            <?php echo intval($games_played['fastest_time'] ?? 0); ?>
        </span>
    </div>
    
    </div>

    <!-- Accordion for Game History-->
    <div id="content_list_container">
        <h3>Items list</h3>
        <div class="accordion accordion-flush" id="content_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="game-history">
                    <div class="accordion-button collapsed d-flex" data-bs-toggle="collapse" data-bs-target="#item_1"
                        aria-expanded="false" aria-controls="#item_1">
                        <h5 class="content_title fw-bold">Game History</h5>
                    </div>
                </h2>
                    <div id="item_1" class="accordion-collapse collapse" aria-labelledby="flush-heading-1"
                        data-bs-parent="#content_accordion">
                        <div class="accordion-body game-history-items">
                            <?php if (!empty($game_history)): ?>
                                <ul>
                                    <?php foreach ($game_history as $game): ?>
                                        <ul class = game-items>
                                            <?= htmlspecialchars($game['gameId']) ?>
                                            <div class="ms-auto">
                                                <span class="edit-icon" data-app-id="1" data-content-id="1"
                                                    aria-hidden="true"><button type ="button" class ="btn btn-success"> ↻ </button></span>
                                                <span class="delete-icon" data-app-id="1" data-content-id="1"
                                                    aria-hidden="true"><button type ="button" class ="btn btn-danger"> X </button></span>
                                            </div>
                                        </ul>
                                    <?php endforeach; ?>
                                </ul>
                                    <!-- add buttons for add and delete here -->
                            <?php else: ?>
                                <p>No games found.</p>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="friend-list">
                    <div class="accordion-button collapsed d-flex" data-bs-toggle="collapse" data-bs-target="#item_2"
                        aria-expanded="false" aria-controls="item_2">
                        <h5 class="content_title fw-bold">Friends List</h5>
                    </div>
                </h2>
                    <div id="item_2" class="accordion-collapse collapse" aria-labelledby="flush-heading-2"
                        data-bs-parent="#content_accordion">
                        <div class="accordion-body content_text">
                            <!-- Friends List -->
                        <?php if (!empty($friends_list)): ?>
                            <ul>
                                <?php foreach ($friends_list as $friend): ?>
                                    <li>
                                        <?= htmlspecialchars($friend['friend_username']) ?>
                                        <div class="ms-auto">
                                            <span class="delete-icon" data-app-id="1" data-content-id="1"
                                                aria-hidden="true"><button type ="button" class ="btn btn-danger"> X </button></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                                <!-- add buttons for add and delete here -->
                        <?php else: ?>
                            <p>No friends found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

                



    <script>
        const editBtns = document.querySelectorAll(".edit-icon");
        const deleteBtns = document.querySelectorAll(".delete-icon");

        editBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log(btn.dataset.contentId);
            })
        })

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log(btn.dataset.contentId);
            })
        })
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" 
        crossorigin="anonymous"></script>
</body>
</html>