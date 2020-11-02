<?php
    	$conn = OpenCon();
	
	$ID = $_SESSION['id'];
	
	$nameres = $conn->query("SELECT * FROM people WHERE id = '$ID'");
	$namerow = $nameres->fetch_assoc();
    	$infores = $conn->query("SELECT * FROM members WHERE id = '$ID'");
    	$inforow = $infores->fetch_assoc();
	
	$fname = $namerow["first_name"];
	$lname = $namerow["last_name"];
	$image = $inforow["avatar"];
	$phone = $inforow["phone"];
	$email = $inforow["email"];
	echo "<img src='./uploads/$image'> <p style='display:inline-block;'> Logged in as: $fname $lname <br><br> Email: $email <br><br> Phone.nr: $phone </p>";
	
	CloseCon($conn);
?>