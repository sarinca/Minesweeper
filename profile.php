<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once('connect-db.php');
require_once('request-db.php');

$user_id = 1;

$debug_messages = [];
$debug_messages[] = "Page loaded at: " . date('H:i:s');
$debug_messages[] = "User ID: $user_id";
$debug_messages[] = "Request Method: " . $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_messages[] = "POST received!";
    $debug_messages[] = "POST data: " . json_encode($_POST);
    if (!empty($_POST['deleteFriendBtn'])) {
        deleteFriend($user_id, $_POST['friend_id']);
    } elseif (!empty($_POST['deleteGameBtn'])) {
        deleteGame($_POST['game_id']);
    } elseif (isset($_POST['updateProfileBtn'])) {
        $debug_messages[] = "Update profile button clicked!";
        $debug_messages[] = "Username: " . ($_POST['username'] ?? 'EMPTY');
        $debug_messages[] = "PFP URL: " . ($_POST['pfp_url'] ?? 'EMPTY');
        
        updateProfile($user_id, $_POST);
        $debug_messages[] = "updateProfile() called";
        
        $debug_messages[] = "About to redirect...";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$userStats = getUserStats($user_id);
$gamesPlayed = getGamesPlayed($user_id);
$gameHistory = getGameHistory($user_id);
$userFriends = getUserFriends($user_id);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inder&display=swap');

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

        .container {
            margin-left: 220px;
            margin-top: 150px;
            padding: 2rem;
            max-width: 80%
        }

        #name\&stats {
            background: transparent;
            padding: 0;
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
            max-width: 100%;
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
            ;
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

        .btn-danger {
            background-color: #e74c3c;
            border: none;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .btn-danger:hover {
            background-color: #c0392b;
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
                    : '/images/profiles/default.png'; ?>" id="pfp" class="rounded-circle me-2" width="40" height="40">


                <div class="profile-dropdown">
                    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <?php echo htmlspecialchars($userStats['username']); ?>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.html">Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="login_page.html">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <nav class="nav flex-column">
        <ul class="vertical-nav">
            <a class="nav-link" href="home.html">Home</a>
            <a class="nav-link" href="leaderboard.html">Leaderboard</a>
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Shop</a>
        </ul>
    </nav>

    <div class="container mt-4" id="name&stats">
        <div class=user-and-pfp>
            <img src="<?php echo !empty($userStats['profilePicture_path'])
                ? $userStats['profilePicture_path']
                : '/images/profiles/default.png'; ?>" alt="Profile Picture" id="bigPfp" class="rounded-circle me-2"
                width="60" height="60">

            <h1>Hello, <?php echo htmlspecialchars($userStats['username']); ?></h1>
            <!-- Button to Edit PFP & Username -->
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
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
                                    autocomplete="username" required>
                            </div>

                            <!-- Profile Picture URL -->
                            <div class="mb-3">
                                <label for="pfpUrlInput" class="form-label">Profile Picture URL</label>
                                <input type="url" class="form-control" id="pfpUrlInput" name="pfp_url"
                                    placeholder="https://example.com/image.png" autocomplete="url">
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
            <p class="stat-label">Total Score: <span class="stat-box"> Not Implemented Yet </span></p>
            <p class="stat-label">Total Score: <span class="stat-box"><?php echo $userStats['totalScore']; ?></span></p>
            <p class="stat-label">Games Played: <span
                    class="stat-box"><?php echo $gamesPlayed['games_played']; ?></span></p>
            <p class="stat-label">Fastest Time: <span
                    class="stat-box"><?php echo $gamesPlayed['fastest_time'] ?? 'N/A'; ?></span></p>
        </div>

        <!-- Accordion Section -->
        <div class="accordion mt-4" id="dashboardAccordion">

            <!-- Game History Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseGameHistory" aria-expanded="false" aria-controls="collapseGameHistory">
                        Game History
                    </button>
                </h2>
                <div id="collapseGameHistory" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Game ID</th>
                                    <th>Score</th>
                                    <th>Time</th>
                                    <th>Delete?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gameHistory as $game): ?>
                                    <tr>
                                        <td><?php echo $game['gameId']; ?></td>
                                        <td><?php echo $game['score']; ?></td>
                                        <td><?php echo $game['gameTime']; ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="game_id" value="<?php echo $game['gameId']; ?>">
                                                <input type="submit" name="deleteGameBtn" class="btn btn-danger btn-sm"
                                                    value="Delete">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Friends List Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseFriendsList" aria-expanded="false" aria-controls="collapseFriendsList">
                        Your Friends
                    </button>
                </h2>
                <div id="collapseFriendsList" class="accordion-collapse collapse" data-bs-parent="#dashboardAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Friend Username</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userFriends as $friend): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($friend['friend_username']); ?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="friend_id"
                                                    value="<?php echo $friend['friend_id']; ?>">
                                                <input type="submit" name="deleteFriendBtn" class="btn btn-danger btn-sm"
                                                    value="Remove Friend">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
</body>

</html>