<?php //Created by Emelie Wallin 
include '../dbconnection.php';
$con = OpenCon();

$id = $_GET['id'];

if(!($statement = $con->prepare('CALL deleteMember(?)'))) {
	die("Prepare failed: (" . $con->errno . ")" . $con->error);
}
else {
	$statement->bind_param('i', $id);
}

if(!$statement->execute()) {
	die("Execute failed: (" . $statement->errno . ")" . $statement->error);
}
else{
	echo '1'; //return value if everything goes well

	$email = "mli.wallin@gmail.com"; //$newMember['email'] supposed to be email to the member. But we did not want to send out mail to random emails

	$textHead = "Your account has been deleted";

	$textBody = "Hi! Your account has been removed /Computer Club";

	mail($email, $textHead, $textBody);
}

$statement->close();

CloseCon($con);

?>