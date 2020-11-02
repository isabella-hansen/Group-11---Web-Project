<?php //Created by Emelie Wallin
//update list page.
	include '../dbconnection.php';

	$con = OpenCon();

	//get all new applications
	$sql = "SELECT * FROM new_members";
	$result = $con->query($sql);

	$newMembers = "null";
	if ($result->num_rows > 0) {
		$newMembers = $result->fetch_all(MYSQLI_ASSOC);
	}

	CloseCon($con);

	if ($newMembers != "null") {
		foreach ($newMembers as $member) { 
		echo "<li id=", $member['id'], " href='#' class='list-group-item list-group-item-action' style='cursor:pointer' >", $member['firstname'], ' ', $member['lastname'], "</li>";				
		 } 			
 } 

	else { 
		echo "<p><em>No new applications</em></p>";
	 } 