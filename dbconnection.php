<?php 

function OpenCon()
 {

 $db = new mysqli("localhost", "root", "", "computer_club");
if ($db->connect_errno) {
    die("Could not connect to DB: " . mysqli_connect_error());
}
 
 return $db;
 }
 
function CloseCon($conn)
 {
 $conn -> close();
 }

 ?>