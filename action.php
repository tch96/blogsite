<?php
session_start();

// variables needed from the news site, can be passed via POST or SESSION, whichever is more convenient

$action = $_POST['act']; //delete or edit
$posttype = $_POST['posttype']; //story or comment
$postid = $_POST['postid']; //id number of story or comment
?>

<?php
if(!hash_equals($_SESSION['token'], $_POST['token'])){
  die("Request forgery detected");
}

require 'connectDB.php';
if ($action == 'Delete'){
	if($posttype == 'story'){
		$stmt = $mysqli->prepare("DELETE FROM stories WHERE story_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('i', $postid);

		$stmt->execute();

		$stmt->close();
		header('Location: newssite.php');
		exit;
	}

	if($posttype == 'comment'){
		$stmt = $mysqli->prepare("DELETE FROM comments WHERE comment_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('i', $postid);

		$stmt->execute();

		$stmt->close();
		header('Location: newssite.php');
		exit;
	}
}
// handle the likes here
if($action == 'tremendous!'){
	//anchor to current story
	$url = "newssite.php#".$_POST['anchor'];
	//check to see if the user has already liked this story
	$stmt1 = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE story_id=? AND username=?");
	if(!$stmt1){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt1->bind_param('is', $postid,$_SESSION['user']);
	$stmt1->execute();
	$stmt1->bind_result($likecnt);
	$stmt1->fetch();
	$stmt1->close();

	if($likecnt > 0){
		header('Location: '.$url);
		exit;
	}else{
		// add to likes table
		$stmt2 = $mysqli->prepare("INSERT INTO likes (story_id,username) VALUES (?,?)");
		if(!$stmt2){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt2->bind_param('is', $postid,$_SESSION['user']);
		$stmt2->execute();
		$stmt2->close();

		//update the ratings
		$stmt = $mysqli->prepare("UPDATE stories SET ratings = ratings + 1 WHERE story_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('i', $postid);
		$stmt->execute();
		$stmt->close();
		header('Location: '.$url);
		exit;
	}
}

if($action == 'Edit'){
	if($posttype == 'story'){
		$stmt = $mysqli->prepare("SELECT COUNT(*), texts, link, title FROM stories WHERE story_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('i', $postid);
		$stmt->execute();

		$stmt->bind_result($cnt, $storytext,$linktext, $titletext);
		$stmt->fetch();

		$stmt->close();
		if($cnt == 1){
			$_SESSION['originalStoryTxt'] = $storytext;
			$_SESSION['originalStoryLink'] = $linktext;
			$_SESSION['originalStoryTitle'] = $titletext;
			$_SESSION['postid'] = $postid;
			header('Location: editStory.php');
			exit;
		}else{
			echo "There are stories with duplicate id / no story with that id.";
		}
	}

	if($posttype == 'comment'){
		$stmt = $mysqli->prepare("SELECT COUNT(*), texts FROM comments WHERE comment_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('i', $postid);
		$stmt->execute();

		$stmt->bind_result($cnt, $commenttext);
		$stmt->fetch();

		$stmt->close();
		if($cnt == 1){
			$_SESSION['originalCommentTxt'] = $commenttext;
			$_SESSION['postid'] = $postid;
			header('Location: editComment.php');
			exit;
		}else{
			echo "There are comments with duplicate id / no comment with that id.";
		}
	}
}
?>
