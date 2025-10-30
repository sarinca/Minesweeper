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

// function getRequestById($id)  
// {
//     global $db; 

//     $query = "SELECT * FROM requests WHERE reqId = :id";
//     $statement = $db->prepare($query);
//     $statement->bindValue(':id', $id);  //this minimizes security risk

//     $statement->execute();
//     $results = $statement->fetch(); 
//     $statement->closeCursor();

//     return $results;

// }

function getTopPointUsers(){
    global $db;

    $query = "SELECT username, totalScore FROM profile";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

function getUserStats($user_id){
    global $db;

    $query = "SELECT username, totalScore 
            FROM profile
            WHERE userId=:userId";

    $statement = $db->prepare($query);
    $statement->bindValue(':userId', $user_id);

    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();
    return $results;
}

?>