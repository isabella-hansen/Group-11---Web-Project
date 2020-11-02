<!-- Created by Emelie Wallin -->
<?php include '../dbconnection.php'; ?>

<?php 	
	$con = OpenCon();
	$id = $_GET['id'];
	$table = $_GET['table'];

	// two diffrent pages uses this page, check which one before collect data from database
	if( $table == 'new_member'){
		//get all new applications
		$sql = "SELECT * FROM new_members WHERE id = '$id'";
		$result = $con->query($sql);
		$member = $result->fetch_array();
	}

	else {

		//get member-info on selected member
		if(!($statement = $con->prepare('CALL getMember(?)'))) {
		  die("Prepare failed: (" . $con->errno . ")" . $con->error);
		}
		else {
			$statement->bind_param('i', $id);
		}

		if(!$statement->execute()) {
		  die("Execute failed: (" . $statement->errno . ")" . $statement->error);
		}

		$result = $statement->get_result();
		if($result->num_rows > 0){

			$member = $result->fetch_array();
		}
	}
	CloseCon($con);
?>

<div class="d-flex flex-row flex-wrap">

<!-- all inputs are disable, admin is not supposed to change anything-->
	<div class="d-flex flex-column col-md-4 p-2">
	
		<!-- show first name -->
		<lable for="fname">First Name:</lable>
		<input type="text" class="form-control" id="fname" value=<?php if ($table == 'new_member'){ echo $member['firstname']; } else { echo $member['first_name']; } ?> disabled><br>

		<!-- show last name -->
		<lable for="lname">Last Name:</lable>
		<input type="text" class="form-control" id="lname" value=<?php if ($table == 'new_member'){ echo $member['lastname']; } else { echo $member['last_name']; }  ?> disabled><br>

	</div>

	<div class="d-flex flex-column col-md-4 p-2">

		<!-- show email -->
		<lable for="email">Email:</lable>
		<input type="text" class="form-control" id="email" value=<?php echo $member['email']; ?> disabled><br>

		<!-- show phone -->
		<lable for="phone">Phone:</lable>
		<input type="text" class="form-control" id="phone" value=<?php echo $member['phone']; ?> disabled><br>
	
	</div>

	<div class="d-flex flex-column col-md-4 p-2">
	
		<!-- show title -->
		<lable for="title">Title:</lable>
		<input type="text" class="form-control" id="title" value=<?php echo $member['title']; ?> disabled><br>

		<!-- show area -->
		<lable for="title">Research Area:</lable>
		<input type="text" class="form-control" id="area" value=<?php echo $member['area']; ?> disabled><br>
	</div>
</div>
		
<div class="d-flex flex-row col-md justify-content-center">
	<!-- show biography -->
	<div class="col-md-8 p-2">
		<lable for="biography">Biography:</lable>
		<textarea class="form-control" id="biography" style="height:135pt" disabled><?=$member['biography']?></textarea>
	</div>

	<div class="col-md-4 p-2 justify-content-center">
	 <!-- show avatar-->
		<div>
			<lable for="img">Avatar:<br></lable>
			<img class="img-thumbnail" id="img" src=<?php if ($table == 'new_member') { //if data is from a new application
																		if ( $member['image_info'] != '') {
																		echo 'uploads/', $member['image_info']; }
																		else { echo 'uploads/default.jpg'; } //if member doesn't have a avatar, show default image
																	}
																	else { echo 'uploads/',$member['avatar']; } ?> style="width: 125pt; height: 125pt">
		</div>
	</div>
</div>

