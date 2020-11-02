<?php //Created by Emelie Wallin 
include '../dbconnection.php';
$con = OpenCon();


CloseCon($con);
$email = "mli.wallin@gmail.com"; //supposed to be email to the person who did the application. But we did not want to send out mail to random emails

$textHead = "Your application has been declined";

$textBody = "Your application for Computer Club has been declined.'";

mail($email, $textHead, $textBody);
?>