<?php
    $data = json_decode(file_get_contents('php://input'), true);
    
    include '../dbconnection.php';
    $db = OpenCon();
    
   if (!($statement = $db->prepare(
        'SELECT id,title FROM projects WHERE title LIKE CONCAT("%", ?, "%")'))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
   }
    
    $statement->bind_param('s', $data);
  
    if(!$statement->execute()) {    
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }
    
    $result = $statement->get_result();
    $statement->close();

    $projects = $result->fetch_all(MYSQLI_ASSOC);
    $output = array();
    foreach ($projects as $value) {
        $output[] = array(
            'title' => $value["title"],
            'id' => $value["id"]
        );
    }
    echo json_encode($output);
?>
