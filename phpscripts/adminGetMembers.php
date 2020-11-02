<?php  //Created By Mariia Nema 
    include '../dbconnection.php';
    $db = OpenCon();

    // Getting areas 
    if (!($statement = $db->prepare('CALL AdminGetMembers()'))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement2->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if($result->num_rows>0) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $members = array();

        foreach ($results as $value) {
            $members[] = array(
            'name' => $value["name"],
            'id' => $value["id"]
        );
        }
    }        
    CloseCon($db);
?>