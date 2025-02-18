<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION['role'] !== "admin"){
    die("Access denied");
}

try{
    $comment_id = (int) $_GET['id'];

    // fetch user email
    $result = $conn->query("
    SELECT users.email, comments.content FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.id = $comment_id
    ");
    $row = $result->fetch_assoc();
    $user_email = $row['email'];
    $comment_content = $row['content'];

    // approve comment
    $stmt = $conn->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();

    // send email notification
    $subject = "Your comment has been approed!";
    $message = "Your comment: \"$comment_content\" has been approved.";
    $headers = "From: noreply@anagkazo.com";

    mail($user_email, $subject, $headers);

    header("Location: admin_comments.php");
    exit();
}catch (Exception $e){
    echo "Error encountered: " . $e->getMessage();
}