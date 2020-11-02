<?php 

    session_start();
    unset($_SESSION['usr']);
    unset($_SESSION['id']);
    session_destroy();
    
    header("Location: ../Index.php");

?>