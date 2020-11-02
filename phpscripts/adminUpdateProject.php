<?php // Created by Mariia Nema
    $data = json_decode(file_get_contents('php://input'), true);
    
    include '../dbconnection.php';
    $db = OpenCon();
    
    if (!($statement = $db->prepare(
            'CALL AdminUpdateProject(?,?,?,?,?,?,?,?,?)'))) {        
            die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    
    $statement->bind_param('ississsss', 
    $data['id'],
    $data['date_end'],
    $data['full_description'],
    $data['leader'],
    $data['result'],
    $data['short_description'],
    $data['title'],
    $data['areas'],
    $data['members']
    );
  
    if(!$statement->execute()) { 
        echo json_encode(false);
        die();//die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }   
   
    $statement->close();
    CloseCon($db); 
    echo json_encode(true);    
    ?>