<?php 
include("classes/DB.php");
include('classes/Login.php');
$tokenIsValid = false;

if (Login::isLoggedIn()) {

	if (isset($_POST['changepassword'])) {
		
		$oldpassword = $_POST['oldpassword'];
		$newpassword = $_POST['newpassword'];
		$newpasswordrepeat = $_POST['newpasswordrepeat'];
		$user_id = Login::isLoggedIn();

		if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id = :user_id', array(':user_id' => $user_id))[0]['password'])) {

			if ($newpassword == $newpasswordrepeat) {
				
				if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
					
					DB::query('UPDATE users SET password = :newpassword WHERE id = :user_id', array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':user_id' => $user_id));
					echo "Password correctly changed!!!";

				} else {
					echo "Password lenght is not correct. Must be between 6 and 60 characters";
				}

			} else {
				echo "Password don't match!!!";
			}

		} else {
			echo "Incorrect Old Password";
		}

	}

} else {
	
	if (isset($_GET['token'])) {
		
		$token = $_GET['token'];

		if(DB::query('SELECT user_id FROM pass_tokens WHERE token = :token', array(':token' => sha1($token)))) {
			
			$user_id = DB::query('SELECT user_id FROM pass_tokens WHERE token = :token', array(':token' => sha1($token)))[0]['user_id'];
			$tokenIsValid = true;

			if (isset($_POST['changepassword'])) {
		
				$newpassword = $_POST['newpassword'];
				$newpasswordrepeat = $_POST['newpasswordrepeat'];

				if ($newpassword == $newpasswordrepeat) {
					
					if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {
						
						DB::query('UPDATE users SET password = :newpassword WHERE id = :user_id', array(':newpassword' => password_hash($newpassword, PASSWORD_BCRYPT), ':user_id' => $user_id));
						echo "Password correctly changed!!!";
						DB::query('DELETE FROM pass_tokens WHERE user_id', array('user_id' => $user_id));

					} else {
						echo "Password lenght is not correct. Must be between 6 and 60 characters";
					}

				} else {
					echo "Password don't match!!!";
				}

			}

		} else {
			die("Token Invalid");
		}
	} else {
		die("Not Logged In");
	}
}

?>

<h1>Change Password</h1>
<form action="<?php if (!$tokenIsValid) { echo "change-password"; } else { echo "change-password.php?token={$token}"; } ?>" method="post">
	<?php if (!$tokenIsValid) { echo '<input type="password" name="oldpassword" value="" placeholder="Current Password">'; } ?>
	<input type="password" name="newpassword" value="" placeholder="New Password">
	<input type="password" name="newpasswordrepeat" value="" placeholder="Repeat New Password">
	<input type="submit" name="changepassword" value="Change Password">
</form>