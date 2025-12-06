<?php

// -------------------- REGISTER FUNCTIONS -------------------- //
function check_registration($email, $username) {
    global $db;
    $query = "SELECT * FROM user WHERE email = :email OR username = :username";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $results = $statement->fetch(); // fetch() only the first row, fetchAll() every row
    $statement->closeCursor();
    return $results;
}

function register($email, $username, $password) {
    global $db;
    $query = "INSERT INTO user (email, username, password) VALUES (:email, :username, :password)";  
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->execute();

        $statement->closeCursor();
    }
    catch (PDOException $e) 
    {
        echo $e->getMessage();

        // if there is a specific SQL-related error message
        //    echo "generic message (don't reveal SQL-specific message)";
    }
    catch (Exception $e)
    {
       echo $e->getMessage();    // be careful, try to make it generic
    }

    //second query to insert into the profile table
    $queryProfile = "INSERT INTO profile (username, points, totalScore, profilePicture_path) VALUES (:username, 0, 0, NULL)";  
    try {
        $statementP = $db->prepare($queryProfile);
        $statementP->bindValue(':username', $username);
        $statementP->execute();

        $statementP->closeCursor();
    }
    catch (PDOException $e) 
    {
        echo $e->getMessage();
    }
    catch (Exception $e)
    {
       echo $e->getMessage();
    }
}

// -------------------- LOGIN FUNCTIONS -------------------- //
function login($username) {
    global $db;
    $query = "SELECT * FROM user NATURAL JOIN profile WHERE username = :username";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $results = $statement->fetch(); // fetch() only the first row, fetchAll() every row
    $statement->closeCursor();
    return $results;
}

// -------------------- SHOP FUNCTIONS -------------------- //

function getShopItems(){
    global $db;

    $query = "SELECT name, description, price, picPath FROM itemInventory NATURAL JOIN itemDetails";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getUserPoints($currUsername){
    //get the points tied with the current user from the profile table

    global $db;

    $query = "SELECT points FROM profile WHERE username = :currUsername";
    $statement = $db->prepare($query);
    $statement->bindValue(':currUsername', $currUsername);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}

function addPointsForTesting($currUsername){
    //find the user's current points, then add 100
    global $db;

    $query = "UPDATE profile SET points = (points + 100) WHERE username = :currUsername";
    $statement = $db->prepare($query);
    $statement->bindValue(':currUsername', $currUsername);
    $statement->execute();
    // $results = $statement->fetch();
    $statement->closeCursor();
    // return $results;
    return;
}

function handlePurchase($currUsername, $itemName, $itemPrice){
    global $db;

    // step 1: update the user table to subtract the amount of points they have
    $query = "UPDATE profile SET points = (points - :itemPrice) WHERE username = :currUsername";
    $statement = $db->prepare($query);
    $statement->bindValue(':currUsername', $currUsername);
    $statement->bindValue(':itemPrice', $itemPrice);
    $statement->execute();
    $statement->closeCursor();

    //step 2: update the buys table with the item id 
    $query = "INSERT INTO buys (userId, itemId) 
        SELECT (SELECT userId FROM profile WHERE username = :currUsername), 
        (SELECT itemId FROM itemInventory WHERE name = :itemName)";
        $statement = $db->prepare($query);
    $statement->bindValue(':currUsername', $currUsername);
    $statement->bindValue(':itemName', $itemName);
    $statement->execute();
    $statement->closeCursor();
}

// -------------------- LEADERBOARD FUNCTIONS -------------------- //

function getTopPointUsers(){
    global $db;

    $query = "SELECT username, totalScore FROM profile ORDER BY totalScore DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getTopPointFriends($currUserId){
    global $db;

    $query = "SELECT username, totalScore FROM profile WHERE userId in 
    (SELECT (userIdOne + userIdTwo - :currUserId) AS userId 
            FROM friends
            WHERE userIdOne = :currUserId OR userIdTwo = :currUserId) 
    ORDER BY totalScore DESC";
    $statement = $db->prepare($query);
    $statement->bindValue(':currUserId', $currUserId);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getEntriesByMode($mode)  
{
    global $db; 

    $query = "SELECT lead.gameId as gameId, game.gameTime as gameTime, profile.username as username, game.mode as mode
    FROM leaderboardEntry AS lead NATURAL JOIN game NATURAL JOIN profile
    WHERE mode = :mode
    ORDER BY gameTime ASC";
    $statement = $db->prepare($query);
    $statement->bindValue(':mode', $mode);  //this minimizes security risk

    $statement->execute();
    $results = $statement->fetchAll(); 
    $statement->closeCursor();

    return $results;
}

function getUserIdFromUsername($currUsername) {
    global $db; 

    $query = "SELECT userId FROM profile WHERE username = :currUsername";
    $statement = $db->prepare($query);
    $statement->bindValue(':currUsername', $currUsername);  //this minimizes security risk

    $statement->execute();
    $result = $statement->fetch(); 
    $statement->closeCursor();

    return $result;
}

function getEntriesByFriendAndMode($mode, $currUserId) {
    global $db; 

    $query = "WITH MyFriends AS (SELECT (userIdOne + userIdTwo - :currUserId) AS userId 
            FROM friends
            WHERE userIdOne = :currUserId OR userIdTwo = :currUserId)

            SELECT lead.gameId as gameId, game.gameTime as gameTime, profile.username as username, game.mode as mode
            FROM leaderboardEntry AS lead NATURAL JOIN game NATURAL JOIN profile NATURAL JOIN MyFriends
            WHERE mode = :mode
            ORDER BY gameTime ASC";
    $statement = $db->prepare($query);
    $statement->bindValue(':mode', $mode);  //this minimizes security risk
    $statement->bindValue(':currUserId', $currUserId);
    $statement->execute();
    $results = $statement->fetchAll(); 
    $statement->closeCursor();

    return $results;
}

function processFiltering($gameMode, $userFriends, $currUsername){
    global $db;

    $currUserId = getUserIdFromUsername($currUsername);
    $currUserId = $currUserId[0];

    $display_entries = [];

    //step 1: filter based on game mode OR all-time
    if ($gameMode == 'Easy' || $gameMode == 'Medium' || $gameMode == 'Hard'){
        
        if ($userFriends == 'friends'){
            return getEntriesByFriendAndMode($gameMode, $currUserId);
        } else {
            return getEntriesByMode($gameMode);
        }

    } else {

        if ($userFriends == 'friends'){
            return getTopPointFriends($currUserId);
        } else {
            return getTopPointUsers();
        }

    }
}

function timeFiltering($entries, $timeMax){
    // echo "time max: " . $timeMax;
    // //step 3: with display_entries as a param, filter based on time range IF a game mode is selected (not all-time)
    if ($timeMax != 0 && $timeMax !== null) {
        // Filter game entries by completion date
        $entries = array_filter($entries, function($game) use ($timeMax) {
            return $game['gameTime'] < $timeMax; 
        });
    }
    return $entries;
}

// ---------------------- PROFILE FUNCTIONS ---------------------- //

function getUserStats($user_id) {
    global $db;

    $query = "SELECT userId,
                     username,
                     points,
                     totalScore,
                     COALESCE(profilePicture_path, '') AS profilePicture_path
              FROM profile
              WHERE userId = :userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $results;
}



function getGamesPlayed($user_id) {
    global $db;

    $query = "SELECT 
                COUNT(*) AS games_played, 
                MIN(CASE WHEN s.state_status = 'WIN' THEN g.gameTime ELSE NULL END) AS fastest_time 
              FROM game g
              LEFT JOIN state s ON g.gameId = s.gameId
              WHERE g.userId = :userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}

function getGameHistory($user_id) {
    global $db;

    $query = "SELECT 
                g.gameId,
                g.gameTime,
                g.mode,
                s.state_status,
                CASE 
                    WHEN s.state_status = 'WIN' THEN gm.points
                    ELSE 0
                END AS score
              FROM game g
              LEFT JOIN state s ON g.gameId = s.gameId
              LEFT JOIN gamemode gm ON g.mode = gm.mode
              WHERE g.userId = :userId
              ORDER BY g.gameId DESC";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $results;
}

function getUserFriends($user_id) {
    global $db;

    $query = "
        SELECT p.userId AS friend_id, p.username AS friend_username, p.profilePicture_path
        FROM friends f
        JOIN profile p ON p.userId = f.userIdTwo
        WHERE f.userIdOne = :userId

        UNION

        SELECT p.userId AS friend_id, p.username AS friend_username, p.profilePicture_path
        FROM friends f
        JOIN profile p ON p.userId = f.userIdOne
        WHERE f.userIdTwo = :userId
        ";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

    return $results;
}

function deleteFriend($session_id, $user_id) {
    global $db;

    $query = "DELETE FROM friends
              WHERE (userIdOne = :session_id AND userIdTwo = :user_id)
                 OR (userIdOne = :user_id AND userIdTwo = :session_id)";

    $statement = $db->prepare($query);
    $statement->bindValue(':session_id', $session_id);
    $statement->bindValue(':user_id', $user_id);
    $success = $statement->execute();
    $statement->closeCursor();

    return $success;
}

function deleteGame($game_id) {
    global $db;

    $query = "DELETE FROM game
              WHERE gameId = :game_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':game_id', $game_id);
    $success = $statement->execute();
    $statement->closeCursor();

    return $success;
}

function updateProfile($user_id, $post) {
    global $db;
    
    if (!empty($post['username'])) {
        $query = "SELECT username FROM profile WHERE userId = :userId";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':userId', $user_id);
        $stmt->execute();
        $currentUsername = $stmt->fetchColumn();
        $stmt->closeCursor();
        
        // update user table (WE HAVE CASCADE TO UPDATE PROFILE TABLE)
        $query = "UPDATE user SET username = :newUsername WHERE username = :currentUsername";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':newUsername', $post['username']);
        $stmt->bindValue(':currentUsername', $currentUsername);
        $stmt->execute();
        $stmt->closeCursor();
    }
    
    if (!empty($post['pfp_url']) && filter_var($post['pfp_url'], FILTER_VALIDATE_URL)) {
        $query = "UPDATE profile SET profilePicture_path = :path WHERE userId = :userId";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':path', $post['pfp_url']);
        $stmt->bindValue(':userId', $user_id);
        $stmt->execute();
        $stmt->closeCursor();
    }
}

function searchUsers($user_id, $query) {
    global $db;
    
    if (strlen($query) < 2) {
        return [];
    }
    
    // Search for users, excluding current user and existing friends
    $sql = "SELECT p.userId, p.username 
            FROM profile p
            WHERE p.username LIKE :query
            AND p.userId != :current_user
            AND p.userId NOT IN (
                SELECT userIdTwo FROM friends WHERE userIdOne = :current_user
                UNION
                SELECT userIdOne FROM friends WHERE userIdTwo = :current_user
            )
            LIMIT 10";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->bindValue(':current_user', $user_id);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    return $results;
}

function addFriend($user_id, $friend_id) {
    global $db;
    
    $query = "INSERT INTO friends (userIdOne, userIdTwo) VALUES (:user_id, :friend_id)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':friend_id', $friend_id);
    $success = $stmt->execute();
    $stmt->closeCursor();
    
    return $success;
}

function getUserInventory($user_id) {
    global $db;
    $query = "SELECT ii.itemId, ii.name, id.picPath as image_path, COUNT(b.itemId) as quantity
              FROM itemInventory ii
              JOIN itemDetails id ON ii.name = id.name
              LEFT JOIN buys b ON ii.itemId = b.itemId AND b.userId = :user_id
              WHERE b.userId = :user_id
              GROUP BY ii.itemId, ii.name, id.picPath
              ORDER BY ii.name";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

?>
