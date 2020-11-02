<?php //Created by Emelie Wallin
session_start();
include '../dbconnection.php';

$member = json_decode(file_get_contents('php://input'), true);

	
$con = OpenCon();
$response = '0';

//Update memberinfo
if(!($statement = $con->prepare('CALL updateMemberInfo(?, ?, ?, ?, ?, ?, ?)'))) {
	        die("Prepare failed: (" . $con->errno . ")" . $con->error);
	    }
else {
	$statement->bind_param('isssssi',
	$member['id'],
	$member['firstname'], 
	$member['lastname'],
	$member['email'],
	$member['phone'],
	$member['biography'],
	$member['title']);
}

if(!$statement->execute()) {
	die("Execute failed: (" . $statement->errno . ")" . $statement->error);
}
else {
	$response = '1';
}

$statement->close();
$con->next_result();

//update member-areas
if($member['areas'] != 'null') {
	if(!($statement = $con->prepare('CALL removeMember_areas(?)'))) {
		die("Prepare failed: (" . $con->errno . ")" . $con->error);
		}
	else {
		$statement->bind_param('i', $member['id']);
	}

	if(!$statement->execute()) {
		die("Execute failed: (" . $statement->errno . ")" . $statement->error);
	}

	foreach($member['areas'] as $area){
		if(!($statement = $con->prepare('CALL insertMembers_area(?, ?)'))) {
			die("Prepare failed: (" . $con->errno . ")" . $con->error);
			}
		else {
			$statement->bind_param('ii', $member['id'], $area);
		}

		if(!$statement->execute()) {
			die("Execute failed: (" . $statement->errno . ")" . $statement->error);
			$response = '0';
		}
	}
}
CloseCon($con);
echo $response;