<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('connect-db.php');
require('request-db.php');

$user_id = 1; // NOTE: I'LL GET THIS FROM NATALIA LATER? WHATEVER ID THE USER LOGS IN WITH
$user_stats = getUserStats($user_id);
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
</head>

<body>
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
        <div class=statsBottomRow><span id=gamesPlayed> </span><span id=fastestTime>Fastest Time:</span></div>
    
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
                        <div class="ms-auto">
                            <span class="edit-icon" data-app-id="1" data-content-id="1"
                                aria-hidden="true"><button type ="button" class ="btn btn-success"> ✓ </button></span>
                            <span class="delete-icon" data-app-id="1" data-content-id="1"
                                aria-hidden="true"><button type ="button" class ="btn btn-danger"> X </button></span>
                        </div>
                    </div>
                </h2>
                <div id="item_1" class="accordion-collapse collapse" aria-labelledby="flush-heading-1"
                    data-bs-parent="#content_accordion">
                    <div class="accordion-body content_text">
                        This is where we will display a User's previous game history.
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-4nlE0ByD4cUT/L8XfEO+EzSSSsLwCGAMnJkrPy5u3iKD2Yv1HwJVKX3MBvfPMhvB" 
        crossorigin="anonymous"></script>
</body>
</html>