<?php 
include('classes/DB.php');

if (isset($_POST['resetpassword'])) {

	$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
	
	$email = $_POST['email'];
	$user_id = DB::query('SELECT id FROM users WHERE email = :email', array(':email' => $email))[0]['id'];
	DB::query('INSERT INTO pass_tokens VALUES(\'\', :token, :user_id)', array(':token' => sha1($token), ':user_id' => $user_id));

	echo "Email sent! <br> $token";
	
}

?>

<h1>Forgot Password</h1>
<form action="forgot-password" method="post">
	<input type="email" name="email" value="" placeholder="user@example.com">
	<input type="submit" name="resetpassword" value="Reset Password">
</form>