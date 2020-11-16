<?php
// Must be used in all files where there exist functionality based on logged in users 

// Start session to populate _SESSION
session_start();
// $_SESSION['idU'] handels logged in users
// used as a referece from database Utilizator(IDUtilizator)
if(isset($_SESSION['idU']))
	// Cookie set to display dynamic JS menu bar
	setcookie('auth', 'true', 2147483647, "/");
else if(isset($_COOKIE['auth']))
	// Delete cookie if user is not logged in
	setcookie('auth', 'true', time()-3600, "/");

?>