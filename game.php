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

$gameId = $_GET['gameId'];
$userStats = null;


if ($_SESSION["username"] == NULL) {
    // echo "Session not established yet";
} else {
    $user_loggedIn = true;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $userStats = getUserStats($user_id);
    }
}

$gameInfo = getGameInfo($gameId);
$mode = $gameInfo['mode'];
$bombs = $gameInfo['state_bombPlacement'];
$game_state = $gameInfo['state_boxesClicked'];
$gameTime = $gameInfo['gameTime'];

$stateInfo = getGameStateInfo($gameId);

$gamemodeInfo = getGamemodeInfo($mode);

$height = $gamemodeInfo['height'];
$width = $gamemodeInfo['width'];
// echo "game.php loaded";
$userInventory = getUserInventory($user_id);
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
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inder&display=swap');

        body {
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

        #board {
            display: grid;
            grid-template-columns: repeat(<?php echo $width; ?>, 30px);
            grid-template-rows: repeat(<?php echo $height; ?>, 30px);
            gap: 2px;
            background-color: #eeba53ff;
            border: 5px solid #eeba53ff;
        }

        .cell {
            width: 30px;
            height: 30px;
            background-color: #FFE9B1;
            border: 3px solid #FFD788;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
        }

        .cell.clicked {
            background-color: #FFD788;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            line-height: 30px;
        }

        .mine-1 {
            color: blue;
        }

        .mine-2 {
            color: green;
        }

        .mine-3 {
            color: red;
        }

        .mine-4 {
            color: darkblue;
        }

        .mine-5 {
            color: brown;
        }

        .mine-6 {
            color: cyan;
        }

        .mine-7 {
            color: purple;
        }

        .mine-8 {
            color: gray;
        }

        .game-wrapper {
            flex: 1;
            display: grid;
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto auto;
            column-gap: 20px;
            row-gap: 0px;
            padding: 50px;
        }

        #page-name {
            grid-column: 1 / -1;
            grid-row: 1;
            font-family: 'Inder', sans-serif;
            font-size: 36px;
            text-align: center;
            margin-bottom: 10px;
        }

        #top-bar {
            grid-column: 1;
            grid-row: 2;
            display: flex;
            gap: 30px;
            align-items: center;
            align-self: start;
        }

        #game-area {
            grid-column: 2;
            grid-row: 2 / 4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #inventory-items {
            grid-column: 1;
            grid-row: 3;
            align-self: start;
            padding: 15px;
            background: #ffe9a3;
            border: 3px solid #b38b1e;
            border-radius: 4px;
            font-size: 14px;
        }

        .inventory-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #ffc562;
        }

        .inventory-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }

        #timer,
        #mine-counter {
            display: inline-block;
            align-self: flex-start;
            height: auto;
            vertical-align: middle;
            font-family: "Press Start 2P", monospace;
            font-size: 22px;
            color: #222;
            padding: 8px 14px;
            background: #ffe9a3;
            border: 3px solid #b38b1e;
            letter-spacing: 2px;
            border-radius: 4px;
            /* margin-left: 75px; */
            /* margin-top: 20px; */
        }

        .navbar {
            position: relative !important;
        }

        .vertical-nav {
            position: relative !important;
            top: 0px !important;
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
                            <li><a class="dropdown-item" href="game.php?action=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="nav flex-row">
        <ul class="vertical-nav">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link active" href="create_game.php">Play</a>
            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
            <a class="nav-link" href="shop.php" tabindex="-1">Shop</a>
        </ul>

        <div class="game-wrapper">
            <div id="top-bar">
                <div>
                    <div style="font-size: 12px; margin-bottom: 5px; text-align: center;">Timer</div>
                    <span id="timer"><?php echo sprintf('%03d', $gameTime); ?></span>
                </div>
                <div>
                    <div style="font-size: 12px; margin-bottom: 5px; text-align: center;">Mines</div>
                    <span id="mine-counter">
                        <?php
                        $totalMines = substr_count($bombs, '1');
                        echo str_pad($totalMines, 3, '0', STR_PAD_LEFT);
                        ?>
                    </span>
                </div>
            </div>

            <div id="inventory-items">
                <h2> <strong>Items</strong> </h2>
                <?php if (empty($userInventory)): ?>
                    <p class="text-center text-muted">You currently have no items to use.</p>
                <?php else: ?>
                    <div class="inventory-grid">
                        <?php foreach ($userInventory as $item): ?>
                            <div class="inventory-item">
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                    alt="<?php echo htmlspecialchars($item['name']); ?>" class="inventory-image">
                                <h6 class="inventory-name"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="inventory-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div id="game-area">
                <div id="board"></div>

                <script>
                    const gameId = <?php echo $gameId; ?>;
                    const height = <?php echo $height; ?>;
                    const width = <?php echo $width; ?>;
                    let state_status = "<?php echo $stateInfo['state_status']; ?>";
                    // alert("Game status1: " + state_status);

                    const game_state = <?php echo json_encode(str_split($game_state)); ?>;
                    const bombs = <?php echo json_encode(str_split($bombs)); ?>;

                    // var totalMines = substr_count($bombs, '1');
                    // document.getElementById("mine-counter").textContent =
                    //     String(totalMines).padStart(3, '0');

                    const board = document.getElementById("board");

                    var displayTimer = null;
                    var elapsedSeconds = <?php echo $gameTime; ?>;
                    var displayPaused = false;

                    const totalMines = <?php echo substr_count($bombs, '1'); ?>;

                    for (let row = 0; row < height; row++) {
                        for (let col = 0; col < width; col++) {
                            const index = row * width + col;
                            const isClicked = game_state[index] === '1';
                            const isBomb = bombs[index] === '1';

                            const cell = document.createElement("div");
                            cell.classList.add("cell");

                            cell.dataset.row = row;
                            cell.dataset.col = col;

                            if (isClicked) {
                                cell.classList.add("clicked");
                                if (isBomb) {
                                    cell.textContent = "💣";
                                } else {
                                    let mineCount = 0;
                                    mineCount += countSurroundingMines(row, col)

                                    cell.textContent = mineCount > 0 ? mineCount : ""; // if no adjacent mines, leave blank, otherwise show count

                                    cell.classList.remove(
                                        "mine-1", "mine-2", "mine-3", "mine-4",
                                        "mine-5", "mine-6", "mine-7", "mine-8"
                                    );

                                    if (mineCount > 0) {
                                        cell.classList.add("mine-" + mineCount);
                                    }
                                }
                            }

                            board.appendChild(cell);
                        }
                    }

                    // document.getElementById("mine-counter").textContent = String(totalMines - flagsPlaced).padStart(3, '0');

                    const cells = document.querySelectorAll(".cell");

                    var gameStarted = false;


                    cells.forEach(cell => {

                        // left click (javascript is so weird guys)
                        cell.addEventListener("click", () => {
                            const row = parseInt(cell.dataset.row);
                            const col = parseInt(cell.dataset.col);
                            const index = row * width + col;

                            if (!gameStarted) {
                                gameStarted = true;
                                startDisplayTimer();
                                // alert("Timer started!");
                            }

                            if (cell.classList.contains("flagged")) {
                                return; // Ignore clicks on flagged cells
                            }
                            if (cell.classList.contains("clicked")) {
                                return; // Ignore clicks on already clicked cells
                            }
                            if (state_status === "WIN" || state_status === "LOSE") {
                                return; // Ignore clicks if game is over
                            }


                            if (bombs[index] === '1') {
                                cell.classList.add("clicked");
                                game_state[index] = '1';
                                cell.textContent = "💣";
                                revealMines();
                                state_status = "LOSE";
                                clearInterval(displayTimer);

                            } else {
                                checkMine(row, col);
                                state_status = checkWin();
                                if (state_status === "WIN") {
                                    clearInterval(displayTimer);
                                }
                            }
                            // alert("status = 4 " + state_status);

                            updateDB(game_state, state_status, "<?php echo $mode; ?>");
                            return;
                        });

                        // right click (works perfect make NO edits :D)
                        cell.addEventListener("contextmenu", (e) => {
                            e.preventDefault(); //removed the default context menu

                            if (!gameStarted) {
                                gameStarted = true;
                                startDisplayTimer();
                                // alert("Timer started!");
                            }

                            if (cell.classList.contains("clicked")) {
                                return; // Ignore right-clicks on already clicked cells
                            }


                            if (cell.classList.contains("flagged")) {
                                cell.classList.remove("flagged");
                                cell.textContent = ""; // unflag
                                // document.getElementById("mine-counter").textContent = String(totalMines - flagsPlaced).padStart(3, '0');
                            } else {
                                cell.classList.add("flagged");
                                cell.textContent = "🚩"; // flag
                                // document.getElementById("mine-counter").textContent = String(totalMines - flagsPlaced).padStart(3, '0');
                            }
                        });

                    });

                    function updateDB(game_state, state_status, mode) {
                        const game_state_str = game_state.join('');
                        // alert("Updating DB with state: " + game_state_str + " and status: " + state_status);

                        fetch('request-db.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=${encodeURIComponent('updateGameState')}` +
                                `&gameId=${encodeURIComponent(gameId)}` +
                                `&game_state=${encodeURIComponent(game_state_str)}` +
                                `&state_status=${encodeURIComponent(state_status)}`
                        })
                            .then(response => response.text())
                            .then(data => {
                                // alert('Success: ' + data);
                                if(state_status === "WIN") {
                                    alert('You win!')
                                } else if (state_status === "LOSE") {
                                    alert('Boo! You lose!')
                                }
                            })
                            .catch((error) => {
                                alert('Error: ' + error);
                            });

                        if (state_status === "WIN") {
                            fetch('request-db.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `action=${encodeURIComponent('updatePoints')}` +
                                    `&gameId=${encodeURIComponent(gameId)}` 
                            })
                                .then(response => response.text())
                                .then(data => {
                                    // alert('Points updated: ' + data);
                                })
                                .catch((error) => {
                                    alert('Error updating points: ' + error);
                                });

                            fetch('request-db.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `action=${encodeURIComponent('addLeaderboardEntry')}` +
                                    `&gameId=${encodeURIComponent(gameId)}`
                            })
                                .then(response => response.text())
                                .then(data => {
                                    // alert('Leaderboard entry added: ' + data);
                                })
                                .catch((error) => {
                                    alert('Error adding leaderboard entry: ' + error);
                                });
                        }
                    }

                    function checkWin() {
                        // alert("Checking win condition");
                        for (let i = 0; i < game_state.length; i++) {
                            if (bombs[i] === '0' && game_state[i] === '0') {
                                // alert("Found unclicked non-bomb cell, not a win yet");
                                return "IN PROGRESS"; // found an unclicked non-bomb cell, not a win yet
                            }
                        }
                        // alert("All non-bomb cells clicked, you win!");
                        // All non-bomb cells are clicked, player wins
                        return "WIN"// updateDB(game_state, "WIN"); //   -- NOT YET IMPLEMENTED --
                    }

                    function startDisplayTimer() {
                        displayTimer = setInterval(() => {
                            if (!displayPaused) {
                                elapsedSeconds++;

                                fetch('request-db.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `action=${encodeURIComponent('updateGameTime')}` +
                                        `&gameId=${encodeURIComponent(gameId)}` +
                                        `&gameTime=${encodeURIComponent(elapsedSeconds)}`
                                })
                                    .then(response => response.text())
                                    .then(data => {
                                        // alert('Game time updated: ' + data);
                                    })
                                    .catch((error) => {
                                        alert('Error updating game time: ' + error);
                                    });

                                document.getElementById("timer").textContent = String(elapsedSeconds).padStart(3, '0');
                            }
                        }, 1000);
                    }

                    function revealMines() {  // the vscode ai made this before i could blink o.O
                        // alert("Revealing all mines");
                        for (let row = 0; row < height; row++) {
                            for (let col = 0; col < width; col++) {
                                const index = row * width + col;
                                if (bombs[index] === '1') {
                                    const cell = document.querySelector(`.cell[data-row='${row}'][data-col='${col}']`);
                                    cell.classList.add("clicked");
                                    cell.textContent = "💣";
                                }
                            }
                        }
                    }

                    function countMine(row, col) {
                        // alert("countMine called");
                        if (row < 0 || row >= height || col < 0 || col >= width) {
                            // alert("out of bounds count");
                            return 0; // out of bounds
                        }
                        const index = row * width + col;
                        if (bombs[index] === '1') {
                            // alert("found mine count");
                            return 1;
                        }
                        return 0;
                    }

                    function countSurroundingMines(row, col) {
                        let mineCount = 0;
                        mineCount += countMine(row - 1, col - 1); // top left
                        mineCount += countMine(row - 1, col); // top middle
                        mineCount += countMine(row - 1, col + 1); // top right

                        // left and right
                        mineCount += countMine(row, col - 1);
                        mineCount += countMine(row, col + 1);

                        // bottom 3
                        mineCount += countMine(row + 1, col - 1); // bottom left
                        mineCount += countMine(row + 1, col); // bottom middle
                        mineCount += countMine(row + 1, col + 1); // bottom right
                        return mineCount;
                    }

                    // I HATE recursive functions T_T
                    function checkMine(row, col) {
                        // alert("before bounds checks");
                        // alert("checking cell at (" + row + ", " + col + ")");

                        if (row < 0 || row >= height || col < 0 || col >= width) {
                            // alert("out of bounds check");
                            return; // out of bounds
                        }
                        // alert("after bounds checks");
                        // alert("calculating index");
                        const index = row * width + col;
                        const cell = document.querySelector(`.cell[data-row='${row}'][data-col='${col}']`);

                        if (bombs[index] === '1') {
                            // alert("bomb check");
                            return; // bomb :(
                        }

                        if (cell.classList.contains("clicked")) {
                            // alert("cell already clicked check");
                            return; // already clicked
                        }

                        // Mark cell as clicked 
                        cell.classList.add("clicked");
                        game_state[index] = '1';

                        // alert("counting adjacent mines");
                        // count adjacent mines
                        let mineCount = 0;
                        mineCount += countSurroundingMines(row, col)

                        // alert("marking mine count");

                        cell.textContent = mineCount > 0 ? mineCount : ""; // if no adjacent mines, leave blank, otherwise show count

                        cell.classList.remove(
                            "mine-1", "mine-2", "mine-3", "mine-4",
                            "mine-5", "mine-6", "mine-7", "mine-8"
                        );

                        // add class if mineCount > 0
                        if (mineCount > 0) {
                            cell.classList.add("mine-" + mineCount);
                        }

                        // if no adjacent mines, recursivley check neighbors
                        // alert("recursivley checking neighbors");
                        if (mineCount === 0) {
                            checkMine(row - 1, col - 1); // top left
                            checkMine(row - 1, col); // top middle
                            checkMine(row - 1, col + 1); // top right

                            // left and right
                            checkMine(row, col - 1);
                            checkMine(row, col + 1);

                            // bottom 3
                            checkMine(row + 1, col - 1); // bottom left
                            checkMine(row + 1, col); // bottom middle
                            checkMine(row + 1, col + 1); // bottom right
                        }

                    }


                </script>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>