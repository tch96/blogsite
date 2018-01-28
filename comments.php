<?php
session_start();
$id = $_POST["story_id"];
$content = $_POST["comment"];
$user = $_SESSION['user'];

if(!hash_equals($_SESSION['token'], $_POST['token'])){
  die("Request forgery detected");
}

require 'connectDB.php';


$stmt = $mysqli->prepare("INSERT INTO comments (username,story_id,texts) VALUES (?,?,?)");
if(!$stmt) {
  printf("Query Prep failed: %s\n", $mysqli->error);
  exit;
}
$stmt->bind_param('sss', $user, $id, $content);
$stmt->execute();
$stmt->close();

header('Location: newssite.php');
?>
