<?php 
include('classes/DB.php');
include('classes/Login.php');

if (Login::isLoggedIn()) {
	echo "Logged In";
	echo isLoggedIn();
} else {
	echo "Not Logged In";
}

?>