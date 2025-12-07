<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// log out functionality
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php"); //can change to login if we want
    exit;
}

require_once('connect-db.php');
require_once('request-db.php');

// $user_id = 1;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['search_users'])) {
    header('Content-Type: application/json');
    $query = $_GET['q'] ?? '';
    $results = searchUsers($user_id, $query);
    echo json_encode($results);
    exit;
}

$debug_messages = [];
$debug_messages[] = "Page loaded at: " . date('H:i:s');
$debug_messages[] = "User ID: $user_id";
$debug_messages[] = "Request Method: " . $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_messages[] = "POST received!";
    $debug_messages[] = "POST data: " . json_encode($_POST);
    if (!empty($_POST['deleteFriendBtn'])) {
        deleteFriend($user_id, $_POST['friend_id']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (!empty($_POST['deleteGameBtn'])) {
        deleteGame($_POST['game_id']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['updateProfileBtn'])) {
        updateProfile($user_id, $_POST);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } elseif (isset($_POST['addFriendBtn'])) {
        addFriend($user_id, $_POST['friend_id']);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$userStats = getUserStats($user_id);
$gamesPlayed = getGamesPlayed($user_id);
$gameHistory = getGameHistory($user_id);
$userFriends = getUserFriends($user_id);
$userInventory = getUserInventory($user_id);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon"
        href="https://static.vecteezy.com/system/resources/previews/042/608/027/non_2x/simple-flag-line-icon-free-vector.jpg" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inder&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: rgba(252, 245, 217);
            color: #000;
            font-family: 'Inder', sans-serif;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            background-color: rgba(252, 245, 217);
            border-bottom: 8px solid rgb(243, 236, 210);
        }

        .navbar-parent {
            font-family: 'Inder', sans-serif;
            font-style: normal;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .profile-dropdown {
            background-color: rgba(252, 245, 217);
            width: 100px;
            margin-right: 80px;
        }
        .dropdown-menu {
            margin-right: 50px;
        }

        #profile-select:hover {
            cursor: pointer;
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

        #editPfpBtn {
            background-color: white;
            justify-content: space-between;
            margin-top: 50px;
            margin-left: 400px;
            margin-right: 100px;
            font-size: 24px;
            width: 150px;
            border-radius: 15px;
        }

        #editPfpBtn:hover {
            background-color: #fbe9af;
        }

        .container {
            margin-left: 270px;
            margin-top: 150px;
            padding: 2rem;
            max-width: 80%
        }

        #name\&stats {
            background: transparent;
            padding: 0;
        }

        .nameSpace {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: 100% !important;
        }

        .user-and-pfp {
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        h1 {
            margin-top: 50px;
            margin-left: 10px;
            color: #000000ff;
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 20px;
        }

        #bigPfp {
            margin-top: 50px;
            width: 50px;
            height: 50px;
            object-fit: cover;
            vertical-align: middle;
        }

        .stats-container {
            padding: 15px;
            padding-left: 100px;
            max-width: 90%;
            color: #000000ff;
            font-weight: bold;
            background-color: #f9c74f;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1.5rem 0;
            border-radius: 15px;
        }

        .stat-box {
            padding: 15px;
            width: fit-content;
            display: flex;
            flex-direction: column;
            background-color: #fbe9af;
            color: #000000ff;
            border-radius: 5px;
            padding: 4px 8px;
            margin-left: 6px;
            min-width: 40px;
            text-align: center;
            font-weight: 300px;
        }

        .stats-container p {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0.5rem;
            gap: 30px;
        }

        .stat-label {
            font-size: 24px;
            font-weight: bold;
        }

        hr {
            border: 2px solid rgba(216, 210, 185, 1);
            width: 100%;
        }

        .accordion {
            margin-top: 2rem;
            max-width: 90%;
        }

        .accordion-item {
            border: none;
            border-radius: 10px;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .accordion-button {
            background-color: #ffffffff;
            color: #000000ff;
            font-weight: bold;
            padding: 1.25rem 1.5rem;
            border: none;
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }

        .accordion-button:not(.collapsed) {
            background-color: white;
            color: black;
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: transparent;
        }

        .accordion-title {
            flex: 0 0 auto;
            margin-right: auto;
        }

        .search-inline {
            margin: 0;
            padding: 0;
            flex-shrink: 0;
            margin-right: 1rem;
            margin-left: auto;
        }

        .search-inline .one {
            font-size: 0.8rem;
            width: calc(25em - 2em);
            height: 1.8em;
        }

        .accordion-button::after {
            margin-left: 0;
        }

        .accordion-body {
            padding: 0;
            background: white;
        }

        .table {
            margin-bottom: 0;
            border-style: none;
        }

        .table th {
            background-color: #ffffffff;
            color: black;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #fffacd;
        }

        .table td:last-child {
            text-align: right;
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        /* Search Bar Styles */
        .search {
            overflow: hidden;
            padding: 0 0 1.25em;
            opacity: 0.7;
            cursor: pointer;
            transition: opacity .3s;
        }

        .search:hover,
        .search:focus-within {
            opacity: 1;
        }

        .one {
            font-size: 2rem;
            margin-top: 1rem;
            width: calc(15em - 2em);
            height: 2em;
            z-index: 2;
            transition: transform 0.6s cubic-bezier(.6, 0, .4, 1);
        }

        .two {
            width: calc(100% - 1em);
            height: 100%;
            position: absolute;
            top: 0;
            left: 1em;
            transition: transform 0.6s cubic-bezier(.6, 0, .4, 1);
        }

        .one:before,
        .two:before {
            content: "";
            position: absolute;
            height: 100%;
            width: 1em;
            border: 0.1em solid #000;
        }

        .one:before {
            left: 0;
            border-right: none;
            border-radius: 1em 0 0 1em;
        }

        .two:before {
            right: 0;
            border-left: none;
            border-radius: 0 1em 1em 0;
        }

        .three {
            height: 100%;
            width: calc(100% - 1em);
            overflow: hidden;
            transition: transform 0.6s cubic-bezier(.6, 0, .4, 1);
        }

        .four {
            display: block;
            width: 100%;
            height: 100%;
            border: 0.1em solid #000;
            border-left: none;
            border-right: none;
            background: transparent;
            color: #000;
            font: inherit;
            transition: transform 0.6s cubic-bezier(.6, 0, .4, 1);
        }

        .four:focus {
            outline: none;
        }

        .one,
        .two,
        .three,
        .four {
            transform: translateX(0) !important;
        }

        /* Search Results */
        #searchResults {
            max-height: 300px;
            overflow-y: auto;
            padding: 1rem;
        }

        .search-result-item {
            padding: 10px;
            border: 1px solid #e9ecef;
            margin-bottom: 5px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
        }

        .search-result-item:hover {
            background-color: #fffacd;
        }

        /* INVENTORY CSS */
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .inventory-item {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
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

        .inventory-name {
            font-weight: bold;
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        .inventory-quantity {
            color: #666;
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        /* FRIEND CSS */
        .friends-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .friend-item {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .friend-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #ffc562;
        }

        .friend-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }

        .friend-name {
            font-weight: bold;
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        /* GAME CSS */
        .game-history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .game-item {
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .game-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #ffc562;
        }

        .game-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }

        .game-mode {
            font-weight: bold;
            margin: 0.5rem 0;
            font-size: 1.1rem;
            color: #000;
        }

        .game-status {
            color: #666;
            margin: 0.3rem 0;
            font-size: 0.9rem;
        }

        .game-time,
        .game-score {
            color: #666;
            margin: 0.3rem 0;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar [ Minesweeper Title, User Profile Button ]-->
    <nav class="navbar navbar-expand-lg px-3">
        <div class="container-fluid">
            <a class="navbar-parent">Minesweeper</a>
            <div class="d-flex align-items-center">
                <img src="<?php echo !empty($userStats['profilePicture_path'])
                    ? $userStats['profilePicture_path']
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
                        <li><a class="dropdown-item" href="profile.php?action=logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <nav class="nav flex-column">
        <ul class="vertical-nav">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link" href="create_game.php">Play</a>
            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
            <a class="nav-link" href="shop.php">Shop</a>
        </ul>
    </nav>

    <div class="container mt-4" id="name&stats">
        <div class=user-and-pfp>
            <img src="<?php echo !empty($userStats['profilePicture_path'])
                ? $userStats['profilePicture_path']
                : 'https://i.pinimg.com/custom_covers/222x/85498161615209203_1636332751.jpg'; ?>" alt="Profile Picture"
                id="bigPfp" class="rounded-circle me-2" width="60" height="60">

            <h1 class = "nameSpace">Hello, <?php echo htmlspecialchars($userStats['username']); ?></h1>
            <!-- Button to Edit PFP & Username -->
            <button class="btn" id="editPfpBtn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                Edit
            </button>
        </div>
        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST" action="">
                        <div class="modal-body">

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="usernameInput" class="form-label">New Username</label>
                                <input type="text" class="form-control" id="usernameInput" name="username"
                                    value="<?php echo htmlspecialchars($userStats['username']); ?>" required>
                            </div>

                            <!-- Profile Picture URL -->
                            <div class="mb-3">
                                <label for="pfpUrlInput" class="form-label">Profile Picture URL</label>
                                <input type="url" class="form-control" id="pfpUrlInput" name="pfp_url"
                                    value="<?php echo htmlspecialchars($userStats['profilePicture_path'] ?? ''); ?>"
                                    placeholder="https://example.com/image.png">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="updateProfileBtn" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <hr>
        <div class="stats-container">
            <p class="stat-label">Total Coins: <span class="stat-box"> <?php echo $userStats['points']; ?></span></p>
            <p class="stat-label">Total Score: <span class="stat-box"><?php echo $userStats['totalScore']; ?></span></p>
            <p class="stat-label">Games Played: <span
                    class="stat-box"><?php echo $gamesPlayed['games_played']; ?></span></p>
            <p class="stat-label">Fastest Time: <span class="stat-box">
                    <?php
                    if (!empty($gamesPlayed['fastest_time'])) {
                        $seconds = $gamesPlayed['fastest_time'];
                        $minutes = floor($seconds / 60);
                        $secs = $seconds % 60;
                        echo sprintf('%02d:%02d', $minutes, $secs);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span></p>
        </div>

        <!-- Accordion Section -->
        <div class="accordion mt-4" id="dashboardAccordion">

            <!-- Game History Accordion Item -->
            <!-- Game History Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseGameHistory" aria-expanded="false" aria-controls="collapseGameHistory">
                        Game History
                    </button>
                </h2>
                <div id="collapseGameHistory" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                    <div class="accordion-body" style="padding: 2rem;">
                        <?php if (empty($gameHistory)): ?>
                            <p class="text-center text-muted">No games played yet. Start playing to build your history!</p>
                        <?php else: ?>
                            <div class="game-history-grid">
                                <?php foreach ($gameHistory as $game): ?>
                                    <?php
                                    // Determine which image to use based on game status
                                    $status = strtolower($game['state_status'] ?? '');
                                    if ($status === 'win') {
                                        $gameImage = 'images/finished_game.png';
                                    } elseif ($status === 'loss' || $status === 'lose') {
                                        $gameImage = 'images/finished_game2.png';
                                    } else {
                                        $gameImage = 'images/in_progress.png';
                                    }
                                    ?>
                                    <div class="game-item">
                                        <img src="<?php echo $gameImage; ?>" alt="Game Status" class="game-image">
                                        <h6 class="game-mode"><?php echo htmlspecialchars($game['mode'] ?? 'N/A'); ?></h6>
                                        <p class="game-status">Status:
                                            <strong><?php echo htmlspecialchars($game['state_status'] ?? 'N/A'); ?></strong></p>
                                        <p class="game-time">
                                            Time:
                                            <?php
                                            if (!empty($game['gameTime'])) {
                                                $seconds = $game['gameTime'];
                                                $minutes = floor($seconds / 60);
                                                $secs = $seconds % 60;
                                                echo sprintf('%02d:%02d', $minutes, $secs);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </p>
                                        <p class="game-score">Score: <?php echo $game['score'] ?? 'N/A'; ?></p>
                                        <form method="post" style="margin: 0;">
                                            <input type="hidden" name="game_id" value="<?php echo $game['gameId']; ?>">
                                            <input type="submit" name="deleteGameBtn" class="btn btn-danger btn-sm"
                                                value="Delete">
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Friends List Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFriendsList" aria-expanded="false" aria-controls="collapseFriendsList">
                        <span class="accordion-title">Your Friends</span>
                        <label class="search search-inline" onclick="event.stopPropagation()">
                            <div class="one">
                                <div class="two">
                                    <div class="three">
                                        <input type="search" class="four" id="friendSearch"
                                            placeholder="Search users..." onclick="event.stopPropagation()" />
                                    </div>
                                </div>
                            </div>
                        </label>
                    </button>
                </h2>
                <div id="collapseFriendsList" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                    <div class="accordion-body" style="padding: 2rem;">
                        <!-- Search Results -->
                        <div id="searchResults"></div>

                        <hr>

                        <!-- Current Friends List -->
                        <?php if (empty($userFriends)): ?>
                            <p class="text-center text-muted">No friends yet. Search for users to add friends!</p>
                        <?php else: ?>
                            <div class="friends-grid">
                                <?php foreach ($userFriends as $friend): ?>
                                    <div class="friend-item">
                                        <img src="<?php echo !empty($friend['profilePicture_path'])
                                            ? htmlspecialchars($friend['profilePicture_path'])
                                            : 'https://i.pinimg.com/custom_covers/222x/85498161615209203_1636332751.jpg'; ?>"
                                            alt="<?php echo htmlspecialchars($friend['friend_username']); ?>"
                                            class="friend-image rounded-circle">
                                        <h6 class="friend-name"><?php echo htmlspecialchars($friend['friend_username']); ?></h6>
                                        <form method="post" style="margin: 0;">
                                            <input type="hidden" name="friend_id" value="<?php echo $friend['friend_id']; ?>">
                                            <input type="submit" name="deleteFriendBtn" class="btn btn-danger btn-sm"
                                                value="Remove">
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Inventory Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                        Inventory
                    </button>
                </h2>
                <div id="collapseInventory" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                    <div class="accordion-body" style="padding: 2rem;">
                        <?php if (empty($userInventory)): ?>
                            <p class="text-center text-muted">No items in inventory. Visit the shop to purchase items!</p>
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
                </div>
            </div>

        </div>
        <!-- End Accordion Section -->

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log("=== DEBUG START ===");
        <?php foreach ($debug_messages as $msg): ?>
            console.log(<?php echo json_encode($msg); ?>);
        <?php endforeach; ?>
        console.log("=== DEBUG END ===");
    </script>
    <script>

        // Search for users
        let searchTimeout;
        document.getElementById('friendSearch').addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch('profile.php?search_users=1&q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(users => {
                        const resultsDiv = document.getElementById('searchResults');

                        if (users.length === 0) {
                            resultsDiv.innerHTML = '<p class="text-muted" style="padding: 1rem;">No users found</p>';
                            return;
                        }

                        resultsDiv.innerHTML = users.map(user => `
                            <div class="search-result-item">
                                <span><strong>${user.username}</strong></span>
                                <form method="post" style="margin: 0;">
                                    <input type="hidden" name="friend_id" value="${user.userId}">
                                    <button type="submit" name="addFriendBtn" class="btn btn-primary btn-sm">
                                        Add Friend
                                    </button>
                                </form>
                            </div>
                        `).join('');
                    })
                    .catch(error => console.error('Search error:', error));
            }, 300);
        });
    </script>
</body>

</html>