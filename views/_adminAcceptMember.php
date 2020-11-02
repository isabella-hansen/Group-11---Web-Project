<!-- Created by Emelie Wallin -->
<?php include '../dbconnection.php'; ?>
<?php 	
	$con = OpenCon();

	//get all membership applications
	$sql = "SELECT * FROM new_members";
	$result = $con->query($sql);

	$newMembers = "null";
	if ($result->num_rows > 0) {
		$newMembers = $result->fetch_all(MYSQLI_ASSOC);
	}

	CloseCon($con);
?>

<div class="d-flex md-10 justify-content-center">
	<h4>New applications</h4>
	<br>
</div>

<div class="container-fluid"> 

	<div class="row">
		<div class="col-md-3">
		<label for "newMembers">Incoming applications:</label>
		<!-- list all membership applications -->
			<ul id="newMembers" class="list-group overflow-auto" style="height:100pt">
				<?php if ($newMembers != "null") {
					foreach ($newMembers as $member) { ?>
						<li id="<?php echo $member['id']; ?>" href="#" class="list-group-item list-group-item-action" style="cursor:pointer;" ><?=$member['firstname'], ' ', $member['lastname']?></li>				
					<?php } 		
					} 

				else { ?>  
					 <p><em>No new applications</em></p>
				<?php }
				?>
			</ul>
			<br>
		</div>

		<!-- default for member-view-->
		<div class="d-flex justify-content-center flex-column col-md-9">
			<div id="newMemberInfo">
				<div class="d-flex flex-row flex-wrap">

					<div class="d-flex flex-column col-md-4 p-2">
		
						<!-- first name -->
						<lable for="fname">First Name:</lable>
						<input type="text" value="" class="form-control" id="fname" disabled><br>

						<!-- last name -->
						<lable for="lname">Last Name:</lable>
						<input type="text" value="" class="form-control" id="lname" disabled><br>
					</div>
	
					<div class="d-flex flex-column col-md-4 p-2">

						<!-- email -->
						<lable for="email">Email:</lable>
						<input type="text" value="" class="form-control" id="email" disabled><br>

						<!-- phone -->
						<lable for="phone">Phone:</lable>
						<input type="text" value="" class="form-control" id="phone" disabled><br>	
					</div>

					<div class="d-flex flex-column col-md-4 p-2">
						
						<!-- title -->
						<lable for="title">Title:</lable>
						<input type="text" value=""class="form-control" id="title" disabled><br>

						<!-- research area -->
						<lable for="title">Research Area:</lable>
						<input type="text" value="" class="form-control" id="area" disabled><br>
					</div>
				</div>
		
				<div class="d-flex flex-row flex-wrap col-md">
					
					<!-- biography-->
					<div class="col-md-8 p-2">
						<lable for="biography">Biography:</lable>
						<textarea class="form-control" id="biography" style="height:135pt" disabled></textarea>
					</div>
					
					<!-- avatar -->
					<div class="col-md-4 p-2">
						<div>
							<lable for="img">Avatar:<br></lable>
							<img class="img-thumbnail" id="img" src="uploads/default.jpg" value="" style="width: 125pt; height: 125pt">
						</div>
					</div>
				</div>
			</div>
		
			<!-- buttons -->
			<div class="d-flex flex-row col-md p-2 justify-content-center">
				<div class="p-2">
					<button id="accept" type="button" class="btn btn-info">Accept <i class="fa fa-check"></i></button>
					<button id="loading" class="btn btn-info" style="display:none;">
						<span class="spinner-border spinner-border-sm"></span>
						Loading..
					</button>
				</div>
				<div class="p-2">
					<button id="decline" type="button" class="btn btn-info">Decline <i class="fa fa-times"></i></button>
					<button id="loading2" class="btn btn-info" style="display:none;">
						<span class="spinner-border spinner-border-sm"></span>
						Loading..
					</button>
				</div>
			</div>

			<!-- alert-place -->
			<div class="d-flex col-md justify-content-center">
				<div id="alertMessage"></div>
			</div>
		</div>
	</div>
</div>
<script src="javascripts/acceptmemberpage.js"></script>