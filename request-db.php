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

function processFiltering($gameMode, $userFriends, $timeRange){
    global $db;

    $display_entries = [];

    //step 1: filter based on game mode OR all-time
    if ($gameMode == 'Easy' || $gameMode == 'Medium' || $gameMode == 'Hard'){
        $display_entries = getEntriesByMode($gameMode);
    } else {
        $display_entries = getTopPointUsers();
    }

    return $display_entries;

    // //step 2: with display_entries as a param, filter based on user friends
    // if ($userFriends == 'friends'){
    //     //filter by friends only with the current user
    //     //TODO: write this function
    // }

    // //step 3: with display_entries as a param, filter based on time range IF a game mode is selected (not all-time)
    // if ($gameMode != 'allPoints'){
    //     //TODO: filter game entries by completion date
    // }
}

/* Functions for creating the game **/ 

function getGamemodeInfo($mode){
    global $db;

    $query = "SELECT height, width, numBombs FROM gamemode WHERE mode = :mode";
    $statement = $db->prepare($query);
    $statement->bindValue(':mode', $mode);

    $statement->execute();
    $results = $statement->fetch(); 
    $statement->closeCursor();
    
    return $results;
}

function addNewGame(){
    
}

?>