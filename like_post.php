<?php
include "db.php";
session_start();

if (isset($_GET["post_id"])) {
    $post_id = $_GET["post_id"];
    $user_id = $_SESSION["user_id"];
    $status = "like";
    try {
        $result = $conn->query("select count(*) as count, status from likes where user_id = $user_id and post_id = $post_id");
        $row = $result->fetch_assoc();
        $user_count = $row["count"];
        $user_status = $row["status"];
        if ($user_count > 0){
            if ($user_status !== "like"){
                $conn->query("update posts set total_likes = total_likes + 1, total_unlikes = total_unlikes - 1 where id = $post_id
                ");
                $conn->query("update likes set status = 'like' where user_id = $user_id and post_id = $post_id");
            }
        }else{
            $stmt = $conn->prepare("insert into likes (user_id, post_id, status) values (?,?,?)");
            $stmt->bind_param("iis", $user_id,$post_id,$status);
            $stmt->execute();
            $conn->query("update posts set total_likes = total_likes + 1 where id = $post_id");
        }
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}";
    }
}
