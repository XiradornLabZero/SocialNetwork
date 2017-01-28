<?php 
include("classes/DB.php");
include('classes/Login.php');

if (!Login::isLoggedIn()) {
	die("Not Logged In");
}


if (isset($_POST['confirm'])) {

	if (isset($_POST['alldevices'])) {

		DB::query('DELETE FROM login_tokens WHERE user_id = :user_id', array(':user_id' => Login::isLoggedIn()));
		
	} else {
		if (isset($_COOKIE['SNID'])) {

			DB::query('DELETE FROM login_tokens WHERE token = :token', array(':token' => sha1($_COOKIE['SNID'])));

		}

		setcookie('SNID', '1', time()-3600);
		setcookie('SNID_', '1', time()-3600);

	}

}

?>


<h1>Logout of your account?</h1>
<p>Are you shure you'd like to logout?</p>
<form action="logout" method="post">
	<input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br>
	<input type="submit" name="confirm" value="Confirm">
</form>