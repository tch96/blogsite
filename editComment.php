<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title>Edit Comment</title>
  <link rel="stylesheet" type="text/css" href="style1.css">
</head>

<?php
session_start();

// variables needed from the news site

$postid = $_SESSION['postid']; //id number of comment
?>
<!-- Pre populate the form -->

<body>
  <div id="titlebar">
    <span> DoChen News Network </span>
  </div>
  <div id="undertitlebar">
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
    <p><b>Update Comment:</b></p>
    <textarea placeholder="Write comment here..." rows="5" cols="80" name="updatedComment" required><?php echo $_SESSION['originalCommentTxt']; ?>
    </textarea><br>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" value="Update Comment">
</form>
</div>

<?php
// update the comments table in database


if(isset($_POST['updatedComment'])){
  if(!hash_equals($_SESSION['token'], $_POST['token'])){
    die("Request forgery detected");
  }
  require 'connectDB.php';
  $stmt = $mysqli->prepare("UPDATE comments SET texts=?  WHERE comment_id=?");
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }

    $stmt->bind_param('si',$_POST['updatedComment'], $postid);

    $stmt->execute();

    $stmt->close();
    header('Location: newssite.php');
    exit;
}
?>

</body>

</html>
