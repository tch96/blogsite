<?php
session_start();
$username = $_POST["username"];
$pswguess = $_POST["psw"];
?>

<?php
require 'connectDB.php';

// Use a prepared statement
$stmt = $mysqli->prepare("SELECT COUNT(*),username,saltedhash FROM user WHERE username=?");

// Bind the parameter
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind the results
$stmt->bind_result($cnt, $user_id, $pwd_hash);
$stmt->fetch();
$stmt->close();
// Compare the submitted password to the actual password hash
if($cnt == 1 && password_verify($pswguess, $pwd_hash)){
	// Login succeeded!
	// Redirect to your target page
	$_SESSION['user'] = $user_id;
	$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
	header('Location: newssite.php');
	exit;
} else{
	// Login failed; redirect back to the login screen
	echo "Wrong Username/Password <br> <br>";
	echo "<a href='newssite.php'>Return to Homepage</a>";
}
?>
