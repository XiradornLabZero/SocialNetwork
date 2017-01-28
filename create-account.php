<?php
/**
 * Create users file for create users
 */
include("classes/DB.php");

if (isset($_POST['createaccount'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];

	if (!DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $username))) {

		if (strlen($username) >= 3 && strlen($username) <= 32) {

			if (preg_match('/[A-Za-z0-9_]+/', $username)) {

				if (strlen($password) >= 6 && strlen($password) <= 60) {

					if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

						if(!DB::query('SELECT email FROM users WHERE email = :email', array(':email' => $email))) {

							DB::query('INSERT INTO users VALUES (\'\', :username, :password, :email, \'0\')', array(':username' => $username, ':password' => password_hash($password, PASSWORD_BCRYPT), ':email' => $email));

							echo "Creation Success";

						} else {
							echo "Email not valid or in use";
						}

					} else {
						echo "Invalid Email";
					}

				} else {
					echo "Invalid Password Length (between 6 and 60 chars)";
				}

			} else {
				echo "Invalid Username (Invalid Chars. Only Alfanumerics and _)";
			}

		} else {
			echo "Invalid Username (more than 3, minus than 32 chars)";
		}

	} else {
		echo "User Alredy Exists";
	}

}

?>

<h1>Register Form</h1>
<form class="" action="create-account" method="post">
	<input type="text" name="username" value="" placeholder="Username">
	<input type="password" name="password" value="" placeholder="Password">
	<input type="email" name="email" value="" placeholder="email@example.com">
	<input type="submit" name="createaccount" value="Create Account">
</form>
