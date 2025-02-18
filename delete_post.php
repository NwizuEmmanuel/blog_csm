<?php
include "db.php";

try{
    if (!isset($_GET['id'])){
        throw new Exception("Post ID is missing");
    }
    $id = (int) $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()){
        throw new Exception("Execution failed: " . $stmt->error);
    }
    $stmt->close();
    header("Location: index.php");
    exit();
}catch (Exception $e){
    echo "Error: " . $e->getMessage();
}