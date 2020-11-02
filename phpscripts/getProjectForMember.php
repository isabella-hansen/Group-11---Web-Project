<?php // Created by Emelie Wallin
    
    include '../dbconnection.php';
    $con = OpenCon();

	$id = $_GET['q'];

	//get projects for a specific member
	if(!($statement = $con->prepare('CALL getMemberProjects(?)'))) {
		die("Prepare failed: (" . $con->errno . ")" . $con->error);
	}
	else {
		$statement->bind_param('i', $id);
	}

	if(!$statement->execute()) {
		die("Execute failed: (" . $statement->errno . ")" . $statement->error);
    }

	$result = $statement->get_result();
	$returnArray = array();
	$num_rows = $result->num_rows;
	$returnArray[] = array('rows' => $num_rows);

	if($num_rows > 0){

		$projectresult = $result->fetch_all(MYSQLI_ASSOC);	
		$projects = array();

		foreach($projectresult as $value){
			$projects[] = array(
				'id'=> $value['id'],
				'title'=> $value['title'],
				'short_description'=> $value['short_description'],
				'status'=> $value['status']
			);
		}

		$returnArray[] = array('projects'=> $projects);
	}
    
	//return array of projects
    echo json_encode($returnArray);
    CloseCon($con); 

?>
