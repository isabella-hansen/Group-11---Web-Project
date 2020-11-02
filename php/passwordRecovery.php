<?php
	include '../dbconnection.php';
    	$conn = OpenCon();

    	$Email = $_POST['email'];

    	$IDres = $conn->query("SELECT * FROM members WHERE email = '$Email'");
    	$IDrow = $IDres->fetch_assoc();
	
		if ($Email == $IDrow['email']) {
			$ID = $IDrow['id'];
	
			$Pswdres = $conn->query("SELECT * FROM users WHERE id = '$ID'");
			$Pswdrow = $Pswdres->fetch_assoc();
		
			if ($ID == $Pswdrow['id']) {
				$Pswd = $Pswdrow['password'];
			
				mail($Email, "Forgotten password for Computer_Club", "You have requested a password recovery \n for your member-login at the Computer Club website \n\n Password:$Pswd \n\n Regards: The Computer Club");
			}
		}
		CloseCon($conn);
		header("Location: ../Index.php");  
?>
