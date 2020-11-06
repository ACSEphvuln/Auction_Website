<?php
include 'accesscontrol.php';
if(isset($_SESSION['idU'])){
	session_unset(); 
	session_destroy();
	setcookie("auth", "", time()-3600);
}

header("Location: index.php");
?>