<?php // Created by Emelie Wallin
include '../dbconnection.php';
$con = OpenCon();

$id = $_GET['id'];

//get data from new member that has been accepted
$sql = "SELECT * from new_members WHERE id = '$id'";
$result = mysqli_query($con, $sql);



if($result->num_rows > 0) {
	$newMember = $result->fetch_array();
	

	if($newMember['image_info'] == ''){
		$image = "default.jpg"; 
	}

	else {
		$image = $newMember['image_info'];
	}

	$result->close();
	$con->next_result();

	if(!($statement = $con->prepare('CALL insertNewMember(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))) {
	        die("Prepare failed: (" . $con->errno . ")" . $con->error);
	    }
		else {
			$statement->bind_param('ssssssssss',
			$newMember['firstname'],
			$newMember['lastname'], 
			$newMember['phone'], 
			$newMember['email'], 
			$newMember['title'], 
			$newMember['area'], 
			$newMember['biography'],
			$newMember['username'], 
			$newMember['password'], 
			$image);
		}

		if(!$statement->execute()) {
	        die("Execute failed: (" . $statement->errno . ")" . $statement->error);
	    }

		else{

			$email = "mli.wallin@gmail.com"; //$newMember['email']

			$textHead = "Your application has been accepted";

			$textBody = "Welcome to Computer Club " . $newMember['firstname'] . "! Your application has been approved! Log in at http://localhost/Index.php to apply for interesting projects";

			mail($email, $textHead, $textBody);
		}


	$statement->close();
}
else {
	echo "Someting went wrong";
}

CloseCon($con);

?>