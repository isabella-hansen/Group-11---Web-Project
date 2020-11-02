<?php $conn = OpenCon();
	$prores = $conn->query("SELECT title, short_description, status, id FROM projects");
	$prorow = $prores->fetch_all();
	$pubres = $conn->query("SELECT title, description, date, abstract FROM publications");
	$pubrow = $pubres->fetch_all();
	$peores = $conn->query("SELECT first_name, last_name, id FROM people");
	$peorow = $peores->fetch_all();
	CloseCon($conn);
?>


<div id="default_page" class="row">
	<div class="col-md-6">
		<?php include('./views/_about.php'); ?>
		<h1> Project brief </h2>
		<?php $limiter = 0;
			foreach ($prorow as $proprint) {
				if ($limiter++ > 2) { break; }
				echo "<div><b> $proprint[0] : $proprint[2] </b><br> $proprint[1] </div><br>
				<a class='more_info' href='./project.php?id=$proprint[3]'><div>Read more...</div></a><br><br>"; 
			} echo "<br><br><br>";
		?>
		<h1> Publication brief </h2>
		<?php $pub_id = 0; $btn_id=10;
			foreach ($pubrow as $pubprint) {
				echo "<div><b> $pubprint[0] : $pubprint[2] </b><br> $pubprint[1] </div><br>
				<button class='btn_info' id='$btn_id' onclick='showAbstract($pub_id, $btn_id)'>Read more...</button>
				<div class='abstract' id='$pub_id'> $pubprint[3] </div><br><br><br>"; $pub_id++; $btn_id++;
			} echo "<br><br><br>";
		?>
		<h1> Active members </h2>
		<?php
			foreach ($peorow as $peoprint) {
				echo "<i> $peoprint[0] $peoprint[1] </i> <br><br> "; 
			}
		?>
	</div>	
	<div class="col-md-6">
		<div id="IEEE-card" class="card p-2 mb-2" style="display:none">                    
			<div class="IEEE" id="IEEE">
				<div class="d-flex justify-content-center"> <img src='./css/IEEE.png'/></div>
				<div class="IEEE-title d-flex justify-content-center mb-1"><h5>MDH Authors. Latest Publications</h5></div>
			</div>
		</div>
	</div>	
</div>
    <script src="javascripts/home.js"></script>
<script src="./Script.js" type="text/javascript"></script> 