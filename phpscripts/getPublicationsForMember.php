<?php // Created by Emelie Wallin
    
    include '../dbconnection.php';
    $con = OpenCon();

	$id = $_GET['q'];

	//Get all publication that member is part of
	if(!($statement = $con->prepare('CALL getMemberPublications(?)'))) {
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

		$pubresult = $result->fetch_all(MYSQLI_ASSOC);

		foreach ($pubresult as $publication){

			

			$authors = array();

			$statement->close();
			$result->close();
			$con->next_result();

			//get all authors of one publication
			if(!($statement = $con->prepare('CALL getAuthorPublication(?)'))) {
					die("Prepare failed: (" . $con->errno . ")" . $con->error);
			}
			else {
				$statement->bind_param('i', $publication['pub_id']);
			}
			
			if(!$statement->execute()) {
				die("Execute failed: (" . $statement->errno . ")" . $statement->error);
			}
				
			$result = $statement->get_result();
			if($result->num_rows > 0){
				$publicationauthor = $result->fetch_all(MYSQLI_ASSOC);

				foreach($publicationauthor as $author){
					$authors[] = array (
						'name' => $author['name'],
						'id' => $author['id']
					);
				}
			}

			$publications[] = array (
				'title' => $publication['title'],
				'date' => $publication['date'],
				'description' => $publication['description'],
				'authors' => $authors
			);
		}
		$returnArray[] = array ('publications' => $publications);
	}
    
	//return array of publications
    echo json_encode($returnArray);
    CloseCon($con); 
?>
