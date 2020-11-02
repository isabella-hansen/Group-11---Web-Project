<?php //Created by Emelie Wallin
session_start();
include '../dbconnection.php';

if(isset($_SESSION['usr']) && isset($_FILES['avatar']['name'])){
	$target_dir = '../uploads/';
	$imageName = $_SESSION['usr'] . basename($_FILES['avatar']['name']);
	$target_file = $target_dir . $imageName;

	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	$response = '0';
		
	if ($_FILES['avatar']['size'] <= 500000){
	//if file is right size

		if($imageFileType == 'jpg' || $imageFileType == 'jpeg') {				
		//if file is the right type

			if(move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)){
			//if file-upload was succsessfull -> update database
				$response = '1';
				$id = $_SESSION['id'];
				$con = OpenCon();

				$sql = "SELECT avatar FROM members WHERE id = '$id'";
				$result = mysqli_query($con, $sql);

				//delete old image from server when uploading a new one
				if($result->num_rows > 0){
					$image = $result->fetch_array();
					if($image['avatar'] != 'default.jpg'){
						$delImage = 'uploads/' . $image['avatar'];
						unlink($delImage);
					}
				}

				$con->next_result();

				if(!($statement = $con->prepare('CALL updateImage(?,?)'))) {
					die("Prepare failed: (" . $con->errno . ")" . $con->error);
				}
				else {
					$statement->bind_param('is', $_SESSION['id'], $imageName);
				}

				if(!$statement->execute()) {
					die("Execute failed: (" . $statement->errno . ")" . $statement->error);
					$response = '0';
				}
				else {
					$response = '1';
					

				}
				CloseCon($con);
			}
		}
	}			
	 echo $response;
 }
?>