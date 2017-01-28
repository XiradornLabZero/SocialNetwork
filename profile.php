<?php 
include('classes/DB.php');
include('classes/Login.php');

$username = "";
$isFollowing = false;
$verified = false;
$posts = '';

if (isset($_GET['username'])) {

	if (DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))) {

		$username = DB::query('SELECT username FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['username'];

		$user_id = DB::query('SELECT id FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['id'];
		$verified = DB::query('SELECT verified FROM users WHERE username = :username', array(':username' => $_GET['username']))[0]['verified'];
		$follower_id = Login::isLoggedIn();

			

		if (isset($_POST['follow'])) {
			
			if ($user_id != $follower_id) {

				if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id = :follower_id', array(':user_id' => $user_id, ':follower_id' => $follower_id))) {

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

		if (DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id = :follower_id', array(':user_id' => $user_id, ':follower_id' => $follower_id))) {

			$isFollowing = true;
			// echo "Alredy following!";
		}


		if (isset($_POST['unfollow'])) {

			if ($user_id != $follower_id) {

				if (DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id = :follower_id', array(':user_id' => $user_id, ':follower_id' => $follower_id))) {

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

		if (!DB::query('SELECT follower_id FROM followers WHERE user_id = :user_id AND follower_id = :follower_id', array(':user_id' => $user_id, ':follower_id' => $follower_id))) {

			$isFollowing = false;
			// echo "Alredy following!";
		}

		if (isset($_POST['post'])) {
			$postcontent = $_POST['postcontent'];
			$loggedInUserId = Login::isLoggedIn();

			if (strlen($postcontent) > 200 || strlen($postcontent) < 1) {
				die ("incorrect lenght post. must be from 1 to 200 chars");
			}

			if ($loggedInUserId == $user_id) {
				DB::query('INSERT INTO posts VALUES (\'\', :postcontent, NOW(), :author_id, 0)', array(':postcontent' => $postcontent, ':author_id' => $user_id));
			} else {
				die ('Incorrect User ID');
			}

		}

		if (isset($_GET['postid'])) {

			if(!DB::query('SELECT user_id FROM post_likes WHERE post_id = :post_id AND user_id = :user_id', array(':post_id' => $_GET['postid'], ':user_id' => $user_id))) {
				DB::query('UPDATE posts SET likes = likes+1 WHERE id = :postid', array(':postid' => $_GET['postid']));
				DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :user_id)', array(':postid' => $_GET['postid'], ':user_id' => $user_id));
			} else {
				echo "Alredy liked!!!";
			}

		}

		$dbpost = DB::query('SELECT * FROM posts WHERE author_id = :author_id ORDER BY id DESC', array(':author_id' => $user_id));

		foreach ($dbpost as $p) {
			$posts .= htmlspecialchars($p['post']);
			$posts .= "<form action=\"profile.php?username={$username}&postid={$p['id']}\" method=\"post\">
							<input type=\"submit\" name=\"like\" value=\"Like\">
						</form>";
			$posts .= "<hr>";
		}
		
	} else {
		die ("User not find");
	}

}

?>

<h1><?php echo $username; ?> Profile Page <?php echo ($verified) ? '- Verified' : ''; ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<?php if ($user_id != $follower_id): ?>
		<?php if ($isFollowing): ?>
			<input type="submit" name="unfollow" value="Unfollow">
		<?php else: ?>
			<input type="submit" name="follow" value="Follow">
		<?php endif; ?>
	<?php endif; ?>
</form>

<form action="profile.php?username=<?php echo $username; ?>" method="post">
	<textarea name="postcontent" cols="80" rows="8"></textarea>
	<input type="submit" name="post" value="Post">
</form>

<h1>Posts</h1>
<div class="posts">
	<?php echo $posts; ?>
</div>