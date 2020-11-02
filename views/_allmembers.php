<!-- Created by Emelie Wallin -->
<link rel="stylesheet" href="css/_allmemberStyle.css">  

<?php
	$con = OpenCon();

	//get data on all members
	$sql = "SELECT * FROM all_members order by first_name";
	$memberResult = mysqli_query($con, $sql);

	// get all avalible titles
	$sql = "SELECT type FROM titles order by type";
	$typeResult = mysqli_query($con, $sql);

	CloseCon($con);

?>

<div class="d-flex flex-row flex-wrap">
	<div class="col-lg-4 col-md-12 col-sm-12">
		<div class="d-flex col-m flex-column"> 
			<div style="color: white">
				<h4>Find Member</h4>
			</div>
			<br>
			<form>
				<div class="input-group mb">

				<!-- title dropdown -->
					<div class="input-group-prepend">
						<select name="select" class="custom-select btn btn-basic">
							<option value"all">All titles</option>
							<?php 
								if($typeResult->num_rows > 0){
									while($row = mysqli_fetch_array($typeResult)){ ?>
										<option value="<?php echo $row['title'] ?>"><?=$row['type']?></option>
									<?php }
								}
							?>
						</select>
					</div>

					<!-- search input -->
					<input class="form-control" type="text" placeholder="Search member" id="search-input" onkeyup="search(this.value)">
					<div class="input-group-append" >
						<span class="input-group-text" ><i class="fa fa-search" ></i></span>
					</div>
				</div>
			</form>
	
			<br>
			<!-- search result table-->
			<div class="table-responsive overflow-auto" style="height:400pt">
				<table class="table table-light table-hover" id="memberTable">
					<thead class="thead-dark">
						<tr id="head">
							
							<!-- onclick to sort after first- or last name -->
							<th class="pointer" onclick="test('0')">Firstname <i class="fa fa-sort"></i></th>
							<th class="pointer" onclick="test('1')">Lastname <i class="fa fa-sort"></i></th>
							<th>Title</th>
						</tr>
					</thead>
					<tbody id="rows">

					<!-- show all members-->
						<?php 
						if($memberResult->num_rows > 0) {
							while($row = mysqli_fetch_array($memberResult)) {
								$id = $row['id']; ?>

								<tr class="pointer" id=<?php echo $id ?> >
									<td><?=$row['first_name']?></td>
									<td><?=$row['last_name']?></td>
									<td><?=$row['title']?></td>
								</tr>

							<?php }
						}
						else { ?>
							<tr>
								<td>No matching result</td>
							</tr>
						<?php }	?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="d-flex col-md flex-column"> 
		<div id="memberinfo" class="container rounded" style="margin-top:30px;">

		<!-- Default for memberview -->
			<div class="row">
				<div class="col-md-3" style="color: white;">
					<h2>Firstname Lastname</h2>
					<h5><em>Title</em></h5>
					<div><img class="img-thumbnail img-fluid"src="./uploads/default.jpg?>" style="width:150pt"></div>
					<p>Email:
					<br>Phone:</p>
				</div>
				<div class="col-md-9">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a id="" class="nav-link text-info active" data-toggle="tab" href="#biography"><strong>Biography</strong></a>
						</li>

						<li class="nav-item">
							<a id="projects" class="nav-link text-info" data-toggle="tab" href="#projects"><strong>Projects</strong></a>
						</li>

						<li class="nav-item">
							<a id="publications_nav" class="nav-link text-info" data-toggle="tab" href="#publications_nav"><strong>Publications</strong></a>
						</li>
					</ul>
				<div class="card" id="card-description" style="height: 500pt">
					<div class="card-body">
						<div id="text" style="display:inline-block;">

						</div>				
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="javascripts/allmemberpage.js"></script>