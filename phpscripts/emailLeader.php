<?php //Created by Mariia Nema
    session_start();
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($_SESSION['usr']) && $_SESSION['usr'] =='admin') {
        echo json_encode('Error');
        die();
    }
    include '../dbconnection.php';
    $db = OpenCon();

    if (!($statement = $db->prepare('SELECT first_name, last_name FROM people Right JOIN members on members.person=people.id Where members.id = ?'))) {
        echo json_encode('Error');//die("Prepare failed: (" . $db->errno . ")" . $db->error);
        die();
    }
    $statement->bind_param('i', $_SESSION['id']);

    if(!$statement->execute()) {
        echo json_encode('Error');
        die();//die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if($result->num_rows>0) {
        $person=$result->fetch_array();
        CloseCon($db);
        $subject = $person['first_name'] . ' ' . $person ['last_name'] . ' wants to join ' . $data['proj'];
        $email_to = "almaninyo@yandex.ru"; //data['email_to'] 
        
        if (mail($email_to, $subject, $data['msg'])) {
            echo json_encode('Success');//json_encode("Success");
        } 
        else {
            echo json_encode('Error');//json_encode("Error");
        }
    }    
    else {
        echo json_encode('Error');//json_encode("Error");
    }
    
?>

