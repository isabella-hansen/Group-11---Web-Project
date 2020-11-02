<?php // Created by Mariia Nema 

    include '../dbconnection.php';

    $db = OpenCon();

    // Getting projects, its leader and areas 
    if (!($statement = $db->prepare(
        "CALL AdminGetProjectByID (?)"))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $_GET["id"]);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();
    
    if ($result->num_rows > 0) {
	    $proj=$result->fetch_assoc(); 
    }

    else {
	    header('Location: admin_main.php');
	    die();
    }
    $result->close();
    $db->next_result();

    // Getting project members 
    if (!($statement = $db->prepare(
        "CALL AdminGetProjectMembers (?)"))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $_GET["id"]);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    $members = array ();
    if($result->num_rows>0) {
        $results = $result->fetch_all(MYSQLI_ASSOC);               
        foreach ($results as $value) {
            $members[] = array(
                'member_id' => $value['id'],
                'member_fname'=>$value['first_name'],
                'member_lname'=>$value['last_name']                
            );
        }
    }        
    $data = array();
    $data['project']=$proj;
    $data['members']=$members;
    CloseCon($db);
    echo json_encode($data);    
?>