<!DOCTYPE html>
<html>
	<head>
		<title> Homepage </title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">			
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
  		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="./css/_allmemberStyle.css">
		<link rel="stylesheet" href="css/Style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  	
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  		<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	</head>


	<body onresize="showOnResize()" onload="showActiveTab('<?php echo $childView; ?>')">
		<?php include './dbconnection.php';
			session_start(); ?>
		<header>
			<button onclick="showOnButtonClick()" class="btn" id="nav_btn"> <i class="fa fa-bars"></i> Menu </button>
			<img src="https://www.mdh.se/images/18.39fca04516faedec8b2189da/1579852027563/mdh_logo_and_text_white_swe.svg">
			<h1><i>COMPUTER CLUB</i></h1>
		</header>
		<nav id="main_nav">
			<ul id="nav_list">
				<li id="views/_home.php"> <a href="./Index.php"> <div class="list_link"> Home </div> </a> </li>
				<li id="views/_allmembers.php"> <a href="./allMembers.php"> <div class="list_link"> Members </div> </a> </li>
				<li id="views/_projects.php"> <a href="./projects.php"> <div class="list_link"> Projects </div> </a> </li>
				<?php if(isset($_SESSION['usr']) && $_SESSION['usr'] == "admin"){
					echo '<li id="views/_admin_main.php"> <a href="./admin_main.php"> <div class="list_link"> Admin </div> </a> </li>';
				} else if(isset($_SESSION['usr']) && $_SESSION['usr'] != "admin"){
					echo '<li id="views/_memberinfo.php"> <a href="./memberinfo.php?id= +' .$_SESSION['id']. '"> <div class="list_link"> My Page </div> </a> </li>';
				} ?>
				<?php if(isset($_SESSION['usr'])){
						echo "<li> <a href='./php/logout.php'> <div class='list_link'> <i> Log out </i> </div> </a> </li>";
					} else {
						echo "<li id='views/_register.php'> <a href='./register.php'> <div class='list_link'> Register </div> </a> </li><button id='login_btn'> <i> Log in </i> </button>";
					}
				?>
			</ul>
		</nav>
		<div id="login_modal">
			<div class="login_cont">
				<span class="login_close"> &times; </span>
				<div class="login">
        				<form action="./php/login.php" method="POST">
            					<input name="username" type="text" placeholder="username" class="login_value"><br>
            					<input name="password" type="password" placeholder="password" class="login_value"><br>
            					<input type="submit" value="Login" id="login_valuebtn">
						<p> <br>Not a member?<br>Register to become one <a href="./register.php"> here </a> . </p>
						<p> Forgotten your password? <a href="./forgottenPassword.php"> Click here! </a> . </p>
        				</form>
    				</div>
				<span><br></span>
			</div>
		</div>
		<section id="active_art">
			<article>
				<?php include($childView); ?>
			</article>
		</section>
		<footer>
			<ul>
				<?php if(isset($_SESSION['usr']) && $_SESSION['usr'] == "admin") { echo '<img src="./uploads/admin.jpg" style="width:10mm;">'; } ?>
				<li>© 2020 Mälardalens högskola</li>
				<li>Box 883</li>
				<li>721 23 Västerås</li>
				<li>info@mdh.se</li>
				<li>021-10 13 00</li>
			</ul>
		</footer>
	</body>	
	<script src="javascripts/Script.js" type="text/javascript"></script>
	<link rel="stylesheet" href="css/Style.css">
</html>