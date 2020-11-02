<?php //Created by Emelie Wallin
//this page is used for the big member-page

	$con = OpenCon();
	$id = $_GET['id'];
	
	//Get memberinfo on a specific member
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

	$result->close();
	$statement->close();
	$con->next_result();


	//get areas for member
	$sql = "SELECT type FROM members_areas JOIN areas ON members_areas.area = areas.id WHERE members_areas.member = '$id' ";
	$result = mysqli_query($con, $sql);

	$areas = 'null';
	if($result->num_rows > 0){
		$areas = $result->fetch_all(MYSQLI_ASSOC);
	}

	$result->close();
	$con->next_result();

	//check if member is part of any projects
	$sql = "SELECT COUNT(*) FROM projects_members WHERE member = '$id' ";
	$count = mysqli_query($con, $sql);
	$proj = "false";
	$num_proj = $count->fetch_array();

	if ( $num_proj['COUNT(*)']> 0){
		$proj = "true";
	}

	$count->close();
	$con->next_result();

	//check if member is part in any publications
	$sql = "SELECT COUNT(*) FROM publications_people inner join members on publications_people.person = members.person WHERE members.person = '$id' ";
	$count = mysqli_query($con, $sql);
	$pub = "false";

	$num_pub = $count->fetch_array();

	if ( $num_pub['COUNT(*)']> 0){
		$pub = "true";
	}


	CloseCon($con);

?>
<!-- member view-->
<div class="container" style="margin-top:30px; color: white;">
	<div class="row">
		<div class="col-md-3">
			<h2><?=$member['first_name'],' ', $member['last_name']?></h2>
			<h5><em><?=$member['title']?></em></h5>
			<div><img class="img-thumbnail img-fluid"src="./uploads/<?=$member['avatar']?>" style="width:150pt"></div><br>
			<div>
				<p><strong>Email:</strong> <?=$member['email']?>
				<?php if($member['phone'] != null) { ?>
					<br><strong>Phone:</strong> <?=$member['phone']?> 
				<?php } ?>

				<?php if($areas != 'null') { ?>
					<br><strong>Interests:</strong> <?php
					foreach($areas as $area) { ?>
					<br><?=$area['type']?> <?php 
					} 
				} ?> </p>
			</div>
		</div>
		
		<div class="col-md-9">
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
					<a id="bio" class="nav-link text-info active" data-toggle="tab" href="#biography"><strong>Biography</strong></a>
				</li>

				<!-- project tab only shows if member is part on any projects-->
				<?php if ($proj == 'true') { ?>
				<li class="nav-item">
					<a id="projects" class="nav-link text-info" data-toggle="tab" href="#projects"><strong>Projects</strong></a>
				</li>
				<?php } ?>

				<!-- publications tab only shows if member is part on any publications-->
				<?php if ($pub == 'true') { ?>
				<li class="nav-item">
					<a id="pubMember" class="nav-link text-info" data-toggle="tab" href="#pubMember"><strong>Publications</strong></a>
				</li>
				<?php } ?>

				<?php 
				// if a member is logged in, hi/her is able to edit his/her member info
				if (isset($_SESSION['id'])){
					if( $member['id'] == $_SESSION['id']){ ?>
						<li class="nav-item">
							<a id="edit" class="nav-link text-info" data-toggle="tab" href="#editInfo" id="edit"><strong>Edit info</strong></a>
						</li>
					<?php } 
				}?>
			</ul>

			<!-- show biography-->
			<div class="card" id="card-description">
				<div class="card-body">
					<div id="text" class="d-flex flex-column" style="display:block;">
						<p>
						<?php if ($member['biography'] != null){
							echo $member['biography'];
						}
						else{ // if no biography is written, make a default one
							echo "My name is " .$member['first_name']. " " .$member['last_name']. ", and I am a " .$member['title']. " at MDH.";
						}
						?></p>
					</div>
					<div id="biography" style="display:none">
						<p>
						<?php if ($member['biography'] != null){
							echo $member['biography'];
						}
						else{ // if no biography is written, make a default one
							echo "My name is " .$member['first_name']. " " .$member['last_name']. ", and I am a " .$member['title']. " at MDH.";
						}
						?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- script to set sessionStorage id item so memberpage.js script collects the correct member id -->
<script type="text/javascript">
    var id = '<?php echo $id; ?>';
	sessionStorage.setItem('id', id);
</script>
<script src="javascripts/memberpage.js"></script>