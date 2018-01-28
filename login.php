<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<?php session_start();
session_destroy();
?>

<body>
  <div id="titlebar">
    <span> DoChen News Network </span>
  </div>
  <div id="undertitlebar">
    <p> <b> Login Here: </b> </p>
    <form name="Login" action="passcheck.php" method="post">
      Username: <input type="text" name="username" required>
      Password: <input type="password" name="psw" required>
      <input type="submit" value="Sign In">
    </form>

    <p><b>Not Registered?</b> <br></p>

    <button onclick="document.location.href='newssite.php'">View as Guest</button>

    <br> <br> Or Create an User here:
    <form name="CreateUser" action="createUser.php" method="post">
      Username: <input type="text" name="newUser" required>
      Password: <input type="password" name="newpsw" required>
      <input type="submit" name="action" value="Create User">
    </form>
  </div>
</body>

</html>
