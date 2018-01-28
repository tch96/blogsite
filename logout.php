<?php
session_start();
session_destroy();
header('Location: newssite.php');
exit;
?>