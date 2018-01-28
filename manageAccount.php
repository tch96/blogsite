<!DOCTYPE HTML>
<html>

<head>
	<meta charset="utf-8">
	<title>Manage Account</title>
	<link rel="stylesheet" type="text/css" href="style1.css">
</head>
<?php session_start();
$username = $_SESSION['user'];

?>

<body>
	<div id="titlebar">
		<span> DoChen News Network </span>
	</div>
	<div id="undertitlebar">
		<p> Hello, <?php echo $username;?>. Fill out this form to change your password: </p>
		<form name="Login" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
			Old Password: <input type="password" name="oldpsw" required> <br>
			New Password: <input type="password" name="newpsw" required> <br>
			<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
			<input type="submit" value="Change Password"> <br>
		</form>
	


	<?php
	if(isset($_POST['oldpsw'])){
		if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
		}
		$oldpsw =  $_POST['oldpsw'];
		$newpsw = $_POST['newpsw'];

		if ((!preg_match('/^[a-z\d]{2,15}$/i', $newpsw))) {
			echo "<br> ERROR: Password must contain only alphanumeric characters, between 2 to 15 characters long </br>";
			exit;
		}
		require 'connectDB.php';

		$stmt = $mysqli->prepare("SELECT COUNT(*),username,saltedhash FROM user WHERE username=?");

// Bind the parameter
		$stmt->bind_param('s', $username);
		$stmt->execute();

// Bind the results
		$stmt->bind_result($cnt, $user_id, $pwd_hash);
		$stmt->fetch();
		$stmt->close();
// Compare the old password to the actual password hash
		if($cnt == 1 && password_verify($oldpsw, $pwd_hash)){

			$saltedhash = password_hash("$newpsw", PASSWORD_BCRYPT);
			$stmt1 = $mysqli->prepare("UPDATE user SET saltedhash=? WHERE username=?");			

			$stmt1->bind_param('ss',$saltedhash, $username);

			$stmt1->execute();

			$stmt1->close();
			echo "Password Updated <br> <br>";
			echo "<button onclick=\"location.href='newssite.php'\">Return to Homepage</button>";
			exit;
		} else{
			echo "Wrong Old Password <br> <br>";
			echo "<button onclick=\"location.href='newssite.php'\">Return to Homepage</button>";
			exit;
		}
	}
	?>
	</div>

</body>

</html>
