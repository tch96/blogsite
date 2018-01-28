<!DOCTYPE HTML>
<html>

<head>
  <meta charset="utf-8">
  <title>News Site</title>
  <link rel="stylesheet" type="text/css" href="style1.css">

</head>
<?php
session_start();
?>

<body>

<?php if (!isset($_SESSION['user'])): ?>
  <div class="topcorner">
  <?php
    echo "<b>Welcome, Guest</b>&emsp;";
  ?>
  <button class="button login" onclick="location.href='login.php'">Sign In / Register Here</button> <br>
  </div>

<?php else: ?>
  <div class="topcorner">
  <?php
    echo "<b>Welcome, " .$_SESSION['user']."</b>&emsp;";
  ?>
  <button class="button login" onclick="location.href='logout.php'">Sign Out</button> <br>
  <a href='stories.php'>Submit a Story</a> <br>
  <a href='manageAccount.php'>Change My Password</a> <br>
  </div>
<?php endif; ?>
<div id="titlebar">
  <span> DoChen News Network </span>
</div>

<?php
require 'connectDB.php';
?>
<div id="setViewOrder">
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
<span>Sort stories by:</span>
  <input type="submit" name='vieworder' value="timeposted">
  <input type="submit" name='vieworder' value="ratings">
  <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
  <span> <br> </span>
</form>
</div>

<div id="storyfeed">
<?php
  if (isset($_POST['vieworder'])){
    $_SESSION['vieworder'] = $_POST['vieworder'];
  }
  if(!isset($_SESSION['vieworder'])){
    $_SESSION['vieworder'] = 'timeposted';
  }

  if($_SESSION['vieworder'] == 'timeposted'){
    $stmt = $mysqli->prepare("SELECT story_id, username, title, texts, link, timeposted, ratings from stories order by timeposted DESC");
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
  }else{
    $stmt = $mysqli->prepare("SELECT story_id, username, title, texts, link, timeposted, ratings from stories order by ratings DESC");
    if(!$stmt){
     printf("Query Prep Failed: %s\n", $mysqli->error);
     exit;
   }
 }

  $stmt->execute();
  $stmt->bind_result($story_id, $user, $title, $content, $link, $timestamp, $ratings);
  $count = 1;
  while($stmt->fetch()) {
?>
  <div class="stories">
    <a id="<?php echo $count; ?>"> </a>
      <h2 class="storyTitle"> <?php echo $title ?> </h2>
      <span class="subStory"> <?php echo "Posted by: " .$user ."&emsp; Time: " .$timestamp. "<br> <a href=\"". $link ."\">Source</a>  <br> Tremendosity: " .$ratings. " <br> ";?> </span>
      <?php if(!isset($_SESSION['user'])):
      else: ?>
      <form class="TREMENDOUS" action="action.php" method="POST">
        <input type="hidden" name="posttype" value="story"/>
        <input type="hidden" name="postid" value="<?php echo htmlspecialchars($story_id); ?>"/>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type="hidden" name="anchor" value="<?php echo $count?> " />
        <button type="submit" name="act" value="tremendous!">TREMENDOUS!</button>
      </form>
    <?php endif; ?>
      <?php if(!isset($_SESSION['user'])):
         elseif($_SESSION['user'] == $user): ?>
      <form class="mod" action="action.php" method="POST">
          <input type="hidden" name="posttype" value="story"/>
          <input type="hidden" name="postid" value="<?php echo htmlspecialchars($story_id); ?>"/>
          <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
          <input type="submit" name="act" value="Edit"/>
          <input type="submit" name="act" value="Delete"/>
        </form>
        <?php endif; ?>

    <p  class="storyContent">  <?php echo nl2br($content); ?> </p>

    <?php if (!isset($_SESSION['user'])): ?>
    <?php else: ?>
      <form class="commentBox" style="display:block" action="comments.php" method="POST">
        <textarea placeholder="Write comment here..." rows="5" cols="80" name="comment" required></textarea><br>
        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($story_id); ?>">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type="submit" name="action" value="Comment">
      </form>
    <?php endif; ?>

          <form class="viewComments" style="display:block" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."#".$count?>" method="post">
            <input type="hidden" name="cmstory" value="<?php echo htmlspecialchars($story_id);?>"/>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
            <input type="submit" value="View Comments"/>
          </form>

  </div>

<?php $count++; }
  $stmt->close();
?>
</div>
  <div id="commentfeed">
    <?php if (isset($_POST['cmstory'])): ?>
    <?php
      $cmstory = $_POST['cmstory'];
      $cm_stmt = $mysqli->prepare("SELECT comment_id, username, texts, timeposted from comments where story_id = ? order by timeposted DESC");

      if(!$cm_stmt){
         printf("Query Prep Failed: %s\n", $mysqli->error);
         exit;
      }

      $cm_stmt->bind_param('s', $cmstory);
      $cm_stmt->execute();
      $cm_stmt->bind_result($cm_id, $cm_user, $cm_content, $cm_timestamp);
      while ($cm_stmt->fetch()) { ?>
        <div class="comments">
          <span> <?php echo "<b>" .$cm_user. "</b> <i> commented:<br>" .$cm_timestamp. "</i>"; ?> </span>
          <p> <?php echo $cm_content; ?> </p>

          <?php if(!isset($_SESSION['user'])):
          elseif($_SESSION['user'] == $cm_user): ?>
          <form class="mod" action="action.php" method="POST">
              <input type="hidden" name="posttype" value="comment"/>
              <input type="hidden" name="postid" value="<?php echo htmlspecialchars($cm_id); ?>"/>
              <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
              <input type="submit" name="act" value="Edit"/>
              <input type="submit" name="act" value="Delete"/>
            </form>
          <?php endif; ?>
        </div>
      <?php }
      ?>
  <?php endif; ?>
  </div>

</body>
</html>
