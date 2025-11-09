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

    $query = "SELECT name, description, price FROM itemInventory NATURAL JOIN itemDetails";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
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

function processFiltering($gameMode, $userFriends){
    global $db;

    $display_entries = [];

    //step 1: filter based on game mode OR all-time
    if ($gameMode == 'Easy' || $gameMode == 'Medium' || $gameMode == 'Hard'){
        $display_entries = getEntriesByMode($gameMode);
        //also filter by time here
    } else {
        $display_entries = getTopPointUsers();
    }

    //return $display_entries;

    // //step 2: with display_entries as a param, filter based on user friends - IMPLEMENT THIS LATER
    // if ($userFriends == 'friends'){
    //     //filter by friends only with the current user
    //     //TODO: WRITE THIS FUNCTION LATER - need to add a param to this function to capture the user id
    // }

    return $display_entries;
    // }
}

function timeFiltering($entries, $timeMax){
    echo "time max: " . $timeMax;
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
?>