<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user'])){
    die("You must be logged in to comment.");
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$content = $_POST['content'];

$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?,?,?)");
$stmt->bind_param("iis", $post_id, $user_id, $content);
$stmt->execute();

header("Location: index.php");
exit();
?>