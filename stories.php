<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <title>Post Story</title>
  <style>
  p {white-space: pre-wrap;}
  </style>
  <link rel="stylesheet" type="text/css" href="style1.css">
</head>

<?php session_start() ?>

<body>
  <div id="titlebar">
    <span> DoChen News Network </span>
  </div>
<?php if (!isset($_SESSION['user'])):
  echo "You must sign in to submit a Story<br><br>";
  echo "<button onclick=\"location.href='newssite.php'\">Return to Homepage</button>";
  else:
  ?>

<?php if (empty($_POST)): ?>
  <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
    Title:<br> <input type="text" name="title" size="100" required> <br>
    <textarea placeholder="Write story here..." rows="20" cols="100" name="content"></textarea>
    <br>
    Optional Link:<br> <input type="text" name="link" size="100" > <br>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" name="Post" value="Post">
  </form>


<?php else: ?>
  <?php
  $user = $_SESSION["user"];
  $title = $_POST["title"];
  $content = $_POST["content"];
  $link = $_POST["link"];

  if(!hash_equals($_SESSION['token'], $_POST['token'])){
      die("Request forgery detected");
    }

  require 'connectDB.php';


  $stmt = $mysqli->prepare("INSERT INTO stories (username,title,texts,link) VALUES (?,?,?,?)");
  if(!$stmt) {
    printf("Query Prep failed: %s\n", $mysqli->error);
    exit;
  }
  $stmt->bind_param('ssss', $user, $title, $content, $link);
  $stmt->execute();
  $stmt->close();

  echo "Entry created <br>";
  echo "<button onclick=\"location.href='newssite.php'\">Return to Homepage</button>";
  ?>

<?php endif; ?>
<?php endif; ?>

</body>
</html>
