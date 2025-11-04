<?php
// function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
// {
//     global $db; 

//     // good way, minimize sql injection
//     $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority)";  
//     try {
//         // bad way
//         //$statement = $db->query($query);

//         // good way
//         $statement = $db->prepare($query);
//         $statement->bindValue(':reqDate', $reqDate);
//         $statement->bindValue(':roomNumber', $roomNumber);
//         $statement->bindValue(':reqBy', $reqBy);
//         $statement->bindValue(':repairDesc', $repairDesc);
//         $statement->bindValue(':reqPriority', $reqPriority);
//         $statement->execute();

//         $statement->closeCursor();

//         // most likely, there should not be a problem adding a request since 
//         // a primary key of the table is auto_increment
//         // if ($statement->rowCount() == 0)
//         //     echo "Failed to add a request <br/>";
//     }
//     catch (PDOException $e) 
//     {
//         echo $e->getMessage();

//         // if there is a specific SQL-related error message
//         //    echo "generic message (don't reveal SQL-specific message)";
//     }
//     catch (Exception $e)
//     {
//        echo $e->getMessage();    // be careful, try to make it generic
//     }
// }

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
    $query = "SELECT * FROM user WHERE username = :username";
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

    $query = "SELECT username, totalScore 
              FROM profile
              WHERE userId = :userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}


function getGamesPlayed($user_id) {
    global $db;

    $query = "SELECT COUNT(*) AS games_played, 
                     MIN(gameTime) AS fastest_time 
              FROM game
              WHERE userId = :userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}

function getGameHistory($user_id) {
    global $db;

    $query = "SELECT *
              FROM game
              WHERE userId = :userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getUserFriends($user_id) {
    global $db;

    $query = "
        SELECT p.userId AS friend_id, p.username AS friend_username
        FROM friends f
        JOIN profile p ON p.userId = f.userIdTwo
        WHERE f.userIdOne = :userId

        UNION

        SELECT p.userId AS friend_id, p.username AS friend_username
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

// ---------------------- GAME FUNCTIONS ---------------------- //

function getGamemodeInfo($mode){
    global $db;

    $query = "SELECT mode, height, width, numBombs FROM gamemode WHERE mode = :mode";
    $statement = $db->prepare($query);
    $statement->bindValue(':mode', $mode);

    $statement->execute();
    $results = $statement->fetch(); 
    $statement->closeCursor();
    
    return $results;
}

function addNewGame($gameInfo){
    echo "Adding new game...";
    global $db;

    $mode = $gameInfo['mode'];
    $height = $gameInfo['height'];
    $width = $gameInfo['width'];
    $numBombs = $gameInfo['numBombs'];

    echo "received info for mode, height, width, numBombs";

    $totalCells = $height * $width;

    $boxesClicked = array_fill(0, $totalCells, "0");
    $bombPlacement = array_fill(0, $totalCells-$numBombs, "0");
    for ($i = 0; $i < $numBombs; $i++){
        array_push($bombPlacement, "1");
    }
    shuffle($bombPlacement);

    $state_boxesClicked = implode("", $boxesClicked);
    $state_bombPlacement = implode("", $bombPlacement);

    $userId = 1; //$_SESSION['userId'];

    $gameTime = 0;
    echo "querying...";

    try{

        $query = "INSERT INTO game (
        userId, 
        state_boxesClicked, 
        state_bombPlacement, 
        mode, 
        gameTime)
        VALUES (
        :userId,
        :state_boxesClicked,
        :state_bombPlacement,
        :mode,
        :gameTime)";

        $statement = $db->prepare($query);

        $statement->bindValue(':userId', $userId); // is this accessible anywhere?
        $statement->bindValue(':state_boxesClicked', $state_boxesClicked);
        $statement->bindValue(':state_bombPlacement', $state_bombPlacement);
        $statement->bindValue(':mode', $mode);
        $statement->bindValue(':gameTime', $gameTime);
        $statement->execute();
        $statement->closeCursor();

        $gameId = $db->lastInsertId();

        $query2 = "INSERT INTO state (
        gameId,
        state_boxesClicked,
        state_bombPlacement)
        VALUES (
        :gameId,
        :state_boxesClicked,
        :state_bombPlacement)";

        $statement2 = $db->prepare($query2);

        $statement2->bindValue(':gameId', $gameId); // is this accessible anywhere?
        $statement2->bindValue(':state_boxesClicked', $state_boxesClicked);
        $statement2->bindValue(':state_bombPlacement', $state_bombPlacement);
        $statement2->execute();
        $statement2->closeCursor();  

    }
    catch (PDOException $e) {
        echo $e->getMessage(); // make more generic to not leak sensitive data
    }
    catch (Exception $e){
        echo $e->getMessage(); //make more generic to not leak sensitive data
    }

}

/* Functions for updating/playing the game */


?>