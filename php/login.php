<?php
	include '../dbconnection.php';
    	$conn = OpenCon();

    	$username = $_POST['username'];
    	$password = $_POST['password'];

		//cryptates the password to compare with the cryptated saved in the database
		$md5_password = md5($password);	
		if($username=='admin')
		{
			$result = $conn->query("SELECT * FROM users WHERE login = '$username' AND password = '$md5_password'");
    		$row = $result->fetch_assoc();
   
		   	if ($username == $row['login'] && $md5_password == $row['password']) {
            	session_start();
            	$_SESSION['usr'] = $row['login'];
				$_SESSION['id'] = $row['id'];
    		}			
		}
		else 
		{
			$result = $conn->query("SELECT members.id as id, login, password FROM members INNER JOIN users on members.user=users.id WHERE users.login = '$username' AND users.password = '$md5_password'");
    		$row = $result->fetch_assoc();
   
		   	if ($username == $row['login'] && $md5_password == $row['password']) {
            	session_start();
            	$_SESSION['usr'] = $row['login'];
				$_SESSION['id'] = $row['id'];
				
    		}
		}    	
		CloseCon($conn);

		
	
	if (isset($_SESSION['usr']) && isset($_SESSION['id'])) {
		if ($_SESSION['usr'] == "admin" && $_SESSION['id'] == 10) {
			header("Location: ../admin_main.php");
			return;
		} else {
			header("Location: ../memberinfo.php?id= +" .$_SESSION['id']. "");
			return;
		}
	}
	header("Location: ../wrongPassword.php");
?>
