<?php

// Check if user is logged in or not. If not, display link to register.
	$logged_in_user;

	 if(isset($_SESSION['usr']))
	{
		$logged_in_user = "";	
	}
	else 
	{
		$logged_in_user = "<br>Are you a student or researcher and want to join? <a class='text-info' href='./register.php'><strong>Register for membership</strong></a><br><br>";
	}	

	$conn = OpenCon();
	$prores = $conn->query("SELECT title, short_description, status, id FROM projects ORDER by projects.id DESC LIMIT 3");
	$prorow = $prores->fetch_all();
	CloseCon($conn);
?>


<div id="default_page" >
	<div class="row">
		<div class="col-sm-12 col-lg-6">
			<div class="mt-sm-4 mt-lg-6"style="text-align:center">
				<h2 >Welcome to Computer Club!</h2><br>
				<p>The Computer Club is a club where students and researchers can work <br> on different research projects or their personal project of interest. <br>This is a place where students can gain experience by working<br> on real-life projects or by investigating different areas of computer science. </p><br>
				<p><?php echo $logged_in_user; ?></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-sm-12">
			<div id="joke-card" class="p-4 overflow-auto" style="height:20rem;display:none">  
				<div class="p-4 border rounded overflow-auto" style="border-color:white"> <h4 id="joke"></h4></div>
			</div>
		</div>	
	</div>
	<div class="row">
		<div class="col-sm-12 col-lg-6">
			<h2 class="mt-md-4 mb-md-4 mt-sm-2 mb-sm-2" style="text-align:center">Latest projects</h2><br>
			<?php foreach ($prorow as $proprint) { ?>
				<div class="project-card no-gutters border rounded overflow-hidden mb-2 p-3 position-relative">
					<h3 class="title mb-0 d-inline-block"><?=$proprint[0]?>					
					<?php if($proprint[2]=='active') { ?>
						<i class="fa fa-unlock"></i>
					<?php } else {?>
						<i class="fa fa-lock"></i>
					<?php } ?>
					 </h3>
					<p class="short-description card-text"><?=$proprint[1]?></p>
					<p><a href = 'project.php?id=<?=$proprint[3]?>' class='stretched-link text-info'>More</a></p>
				</div>
			<?php }?>
		</div>
	
		<div class="col-lg-6 d-sm-none d-lg-block">
			<div id="IEEE-card" class="card p-2 mb-2" style="display:none">                    
				<div class="IEEE" id="IEEE">
					<div class="d-flex justify-content-center"> <img src='./css/IEEE.png'/></div>
					<div class="IEEE-title d-flex justify-content-center mb-1"><h5>MDH Authors. Latest Publications</h5></div>
				</div>
			</div>
		</div>	
	</div>	
</div>
    <script src="javascripts/home.js"></script>
<script src="./Script.js" type="text/javascript"></script> 