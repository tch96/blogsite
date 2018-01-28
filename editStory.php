<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title>Edit Story</title>
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
  </div id="undertitlebar">

  <form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
    Title:<br> 
    <input type="text" value = "<?php echo $_SESSION['originalStoryTitle']; ?>" name="updatedTitle" size="100" required> <br>
    <textarea rows="20" cols="100" name="updatedStory" required><?php echo $_SESSION['originalStoryTxt'];?></textarea>
    <br>
    Optional Link:<br> 
    <input type="text" value = "<?php echo $_SESSION['originalStoryLink']; ?>" name="updatedLink" size="100" > <br>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" value="Update Story">
  </form>

  <?php
// update the stories table in database
  if(isset($_POST['updatedStory'])){

    if(!hash_equals($_SESSION['token'], $_POST['token'])){
      die("Request forgery detected");
    }

    require 'connectDB.php';
    $stmt = $mysqli->prepare("UPDATE stories SET title=?, texts=?, link=?  WHERE story_id=?");
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }

    $stmt->bind_param('sssi',$_POST['updatedTitle'], $_POST['updatedStory'],$_POST['updatedLink'], $postid);

    $stmt->execute();

    $stmt->close();
    header('Location: newssite.php');
    exit;
  }
  ?>

</body>

</html>
