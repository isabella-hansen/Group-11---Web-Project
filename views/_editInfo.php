<?php // Created by Emelie Wallin
session_start();
include '../dbconnection.php';
$con = OpenCon();

if (isset($_SESSION['id'])) {
	$id = $_SESSION['id'];
	
	//Get memberinfo on a specific member
	if (!($statement = $con->prepare('CALL getMember(?)'))) {
		die("Prepare failed: (" . $con->errno . ")" . $con->error);
	}
	else {
		$statement->bind_param('i', $id);
	}

	if (!$statement->execute()) {
		die("Execute failed: (" . $statement->errno . ")" . $statement->error);
	}

	$result = $statement->get_result();
	if ($result->num_rows > 0) {
		$member = $result->fetch_array();
	}

	$result->close();
	$statement->close();
	$con->next_result();

	//get areas for a specific member
	if (!($statement = $con->prepare('CALL getMemberAreas(?)'))) {
		die("Prepare failed: (" . $con->errno . ")" . $con->error);
	}
	else {
		$statement->bind_param('i', $id);
	}

	if (!$statement->execute()) {
		die("Execute failed: (" . $statement->errno . ")" . $statement->error);
	}

	$result = $statement->get_result();
	$memberAreas = array();
	if ($result->num_rows > 0) {
			$memberAreas = $result->fetch_all(MYSQLI_ASSOC);
	}

	$result->close();
	$statement->close();
	$con->next_result();

	$sql = "SELECT * FROM titles order by type";
	$typeResult = $con->query($sql);
	$resultType = $typeResult->fetch_all(MYSQLI_ASSOC);

	$con->next_result();
	$sql = "SELECT * FROM areas order by type";
	$areaResult = $con->query($sql);

	CloseCon($con);
}
?>
<div class="d-flex flex-column">

	<div class="d-flex col-md-12 flex-wrap">
		<div class="d-flex flex-column col-sm-12 col-md-7">
			
			<!--First name, required and can't be left emty -->
			<form id="fname-form" class="needs-validation" novalidate >  
				<div class="input-group col-sm-12 mb-3">   
					<span class="input-group-prepend">
						<div class="input-group-text">First Name</div>
					</span>
					<input type="text" value=<?php echo $member['first_name']; ?> class="form-control" id="fname" name="fname" required="required">
					<div class="invalid-feedback">Please enter your first name.</div>
				</div>
			</form>

			<!--Last name, required and can't be left emty -->
			<form id="lname-form" class="needs-validation" novalidate>
				<div class="input-group col-sm-12 mb-3">
					<span class="input-group-prepend">
						<div class="input-group-text">Last Name</div>
					 </span>
					<input type="text" value=<?php echo $member['last_name']; ?> class="form-control" id="lname" name="lname" required="required">
					<div class="invalid-feedback">Please enter your last name.</div>
				</div>
			</form>

			<!--Email, required and can't be left emty -->
			<form id="email-form" class="needs-validation" novalidate>
				<div class="input-group col-sm-12 mb-3">
					<span class="input-group-prepend">
						<div class="input-group-text">Email</div>
					 </span>
					<input type="email" value=<?php echo $member['email']; ?> class="form-control" id="email" name="email" required="required">				  
					<div class="invalid-feedback">Please enter a valid email.</div>
				</div>
			</form>

			<!--Phone -->
			<form id="phone-form">
				<div class="input-group col-sm-12 mb-3">
					<span class="input-group-prepend">
						<div class="input-group-text">Phone</div>
					</span>
					<input type="email" value=<?php echo $member['phone']; ?> class="form-control" id="phone" name="phone">
				</div>
			</form>

			<!--choose file for Avatar-->
			<form id="avatar-form" action="" method="post" enctype="multipart/form-data">
				<div class="input-group col-sm-12 mb-3">
					<span class="input-group-prepend">
						<div class="input-group-text">Avatar</div>
					</span>
					<div class="custom-file">
				
						<input type="file" class="custom-file-input" name="avatar" id="avatar">
						<label class="custom-file-label" for="image">Choose file</label>
					</div>
				</div>
			</form> 
		</div>
		<div class="d-flex col-sm-12 col-md-5">
			<div class="d-flex flex-column p-2">

				<!--Title dropdown -->
				<form id="title-form">
					<div class="input-group form-group col-sm-12 mb-3">
						<span class="input-group-prepend">
							<div class="input-group-text">Title</div>
						</span>
						<select class="custom-select btn btn-basic" id="title" name="title">
							<!-- <option value"all">All titles</option> -->
							<?php  
								foreach ($resultType as $row) { ?>
									<option value="<?php echo $row['id'] ?>"><?=$row['type']?></option>
								<?php }
							?>
						</select>
					</div>
				</form>

				<!--Area checkbox -->
			
				<form id="area-form">
					<lable for="area">Reseach Area:</lable>
					<?php 
						if($areaResult->num_rows > 0){
							while($row = $areaResult->fetch_array()){ ?>
								<div class="areas-group-checkbox custom-control custom-checkbox overflow-auto" for="area" id="area">
									<input type="checkbox" class="form-check-input" name="area" id="<?php echo $row['id'] ?>" 
									<?php
										foreach ($memberAreas as $area){
											if ($row['type'] == $area['type']){ ?> checked
										<?php }	
									} ?> ><?=$row['type']?>
								</div>
							<?php }
						} 
					?>	
				</form>
			</div>
		</div>
	</div>	

	<div class="d-flex flex-column col-md-12 mb-3">

		<!--biography -->
		<form id="bio-form">
			
				<div  class="d-flex flex-column col-md-12">
					<lable for="biography">Biography:</lable>
					<textarea class="form-control" id="bioEdit" name"bioEdit" maxlength="2000" onkeyup="showCharsLeft(this,'descr_counter',2000)" style="height:150pt"><?=$member['biography']?></textarea>
					
					<div class="w-100"><small class="float-right">(<span id="descr_counter">2000</span> signs left)</small></div>
				</div>
		</form> 
		<div class="d-flex col-md-12 justify-content-center" >
			<div class="d-flex col-md-3 ">
				<button id="submit" class="btn btn-info" onclick="submitForm('<?php echo $id ?>')">Submit <i class="fa fa-check"></i></button>
				<button id="loadingEdit" class="btn btn-info" style="display:none;">
					<span class="spinner-border spinner-border-sm"></span>
					Loading..
				</button>
			</div>
		</div>
	</div>
	<br>
	<div id="alertMessage"></div>

</div>
	
<script>
//made by Isabella Hansen, selected filename shows
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>
<script src="javascripts/adminCommon.js"></script>
<script src="javascripts/editMemberInfo.js"></script>
