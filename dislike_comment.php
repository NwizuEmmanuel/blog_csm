<?php
include "db.php";

if (isset($_GET["id"]) || isset($_GET["parent_id"])) {
    $id = $_GET["id"];
    $parent_id = $_GET["parent_id"];
    try {
        // add dislike
        $conn->query("UPDATE comments SET total_dislike = total_dislike + 1 WHERE id = $id OR parent_id = $parent_id");

        // register like
        $user_id = $_SESSION["user_id"];
        $stmt = $conn->prepare("insert into users_likes (user_id,comment_id) values (?,?)");
        $stmt->bind_param("ii", $user_id,$id);
        $stmt->execute();
        
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}";
    }
}
