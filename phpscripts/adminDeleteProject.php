<?php
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id=$data['id'];
    include '../dbconnection.php';
    $db = OpenCon();
    if (!($statement = $db->prepare(
        'DELETE FROM projects WHERE projects.id = ?'))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $id);

    if(!$statement->execute()) {    
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }
    $affected_rows = $db->affected_rows;
    $statement->close();
    echo json_encode($affected_rows);    
?>
