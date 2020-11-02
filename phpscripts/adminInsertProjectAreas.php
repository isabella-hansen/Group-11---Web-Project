<?php // Created by Mariia Nema
    $data = json_decode(file_get_contents('php://input'), true);

    include '../dbconnection.php';
    $db = OpenCon();

    if (!($statement = $db->prepare(
            'CALL AdminInsertProjectAreas(?,?)'))) {
            die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }

    $statement->bind_param('is', $data['project_id'], $data['areas']);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $affected_rows = $db->affected_rows;
    $statement->close();
    CloseCon($db); 
    echo json_encode($affected_rows);
    

    ?>