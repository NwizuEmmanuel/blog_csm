<?php
include 'db.php';
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== "admin"){
    die("Access denied");
}

try{
    $comment_id = (int) $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();

    header("Location: admin_comments.php");
    exit();
}catch (Exception $e){
    echo "Error encountered: " . $e->getMessage();
}