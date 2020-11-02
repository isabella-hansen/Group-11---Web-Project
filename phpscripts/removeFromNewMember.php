<?php 
//Created by Emelie Wallin 
include '../dbconnection.php';
$con = OpenCon();

$id = $_GET['id'];
// delete processed applications
$sql = "DELETE from new_members WHERE id = '$id'";
mysqli_query($con, $sql);

CloseCon($con);

?>