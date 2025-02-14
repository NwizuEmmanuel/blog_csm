<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST["title"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?,?)");
    $stmt->bind_param("ss",$title, $content);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}