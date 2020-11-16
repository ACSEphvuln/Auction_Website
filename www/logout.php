<?php
// Log out user
include 'accesscontrol.php';
if(isset($_SESSION['idU'])){
	session_unset(); 
	session_destroy();
	setcookie("auth", "", time()-3600);
}
// Redirect to home page
header("Location: index.php");
?>