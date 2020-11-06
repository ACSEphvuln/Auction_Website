<?php // accesscontrol.php
session_start();
if(isset($_SESSION['idU']))
	setcookie('auth', 'true', 2147483647, "/");
else if(isset($_COOKIE['auth']))
	setcookie('auth', 'true', time()-3600, "/");

?>