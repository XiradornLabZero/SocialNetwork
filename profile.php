<?php 
include('classes/DB.php');
include('classes/Login.php');

$username = "";
$isFollowing = false;
$verified = false;

if (isset($_GET['username'])) {

	if (DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))) {

		$username = DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['username'];

		$user_id = DB::query('SELECT id FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['id'];
		$verified = DB::query('SELECT verified FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['verified'];
		$follower_id = Login::isLoggedIn();

			

		if (isset($_POST['follow'])) {
			
			if ($user_id != $follower_id) {

				if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id', array(':user_id' => $user_id))) {

					if ($follower_id == 0) {
						DB::query('UPDATE users SET verified = 1 WHERE id = :user_id', array(':user_id' => $user_id));
					}

					DB::query('INSERT INTO followers VALUES (\'\', :user_id, :follower_id)', array(':user_id' => $user_id, ':follower_id' => $follower_id));

				} else {
					echo "Alredy following!";
				}

				$isFollowing = true;
			
			} else {
				echo "That's my Profile";
			}

		}

		if (DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id', array(':user_id' => $user_id))) {

			$isFollowing = true;
			// echo "Alredy following!";
		}


		if (isset($_POST['unfollow'])) {

			if ($user_id != $follower_id) {

				if (DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id', array(':user_id' => $user_id))) {

					if ($follower_id == 0) {
						DB::query('UPDATE users SET verified = 0 WHERE id = :user_id', array(':user_id' => $user_id));
					}

					DB::query('DELETE FROM followers WHERE user_id = :user_id AND follower_id = :follower_id', array(':user_id' => $user_id, ':follower_id' => $follower_id));

				}

				$isFollowing = false;
			
			} else {
				echo "That's my Profile";
			}

		}

		if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id', array(':user_id' => $user_id))) {

			$isFollowing = false;
			// echo "Alredy following!";
		}
		
	} else {
		die ("User not find");
	}

}

?>

<h1><?php echo $username; ?> Profile Page <?php echo ($verified) ? '- Verified' : ''; ?></h1>
<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
	<?php if ($user_id != $follower_id): ?>
		<?php if ($isFollowing): ?>
			<input type="submit" name="unfollow" value="Unfollow">
		<?php else: ?>
			<input type="submit" name="follow" value="Follow">
		<?php endif; ?>
	<?php endif; ?>
</form>