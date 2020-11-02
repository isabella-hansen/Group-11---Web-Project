<?php //Isabella has created this page ?>

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
?>

<div>
	<br>
		<div>
			<h2>About The Computer Club!</h2><br>
			<p>The Computer Club is a club where students and researchers can work <br> on different research projects or their personal project of interest. <br>This is a place where students can gain experience by working<br> on real-life projects or by investigating different areas of computer science. </p><br>
			<p><?php echo $logged_in_user; ?></p>
		</div>
</div>
