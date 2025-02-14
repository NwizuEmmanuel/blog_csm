<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST["title"];
    $content = $_POST["content"];

    // handle file upload
    if ($_FILES['image']['name']){
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target = "upload/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }else{
        $image_name = NULL;
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content, image) VALUES (?,?,?)");
    $stmt->bind_param("sss",$title, $content, $image_name);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
</head>
<body>
    <form method="post">
        <input type="text" name="title" placeholder="Title" required><br>
        <textarea name="content" required></textarea><br>
        <input type="file" name="image"><br>
        <button type="submit">Add Post</button>
    </form>
</body>
</html>