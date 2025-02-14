<?php include "db.php";
session_start();
if (!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}
echo "<h3>Welcome " . $_SESSION['user'] . "!</h3>";
echo "<a href='logout.php'>Logout</a>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Simple PHP Blog</title>
</head>
<body>
    <h2>Add a New Post</h2>
    <form action="add_post.php" method="POST">
        <input type="text" name="title" id="title" required placeholder="Post Title">
        <textarea name="content" required placeholder="Post Content"></textarea>
        <button type="submit">Add Post</button>
    </form>
    <h2>All Posts</h2>
    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()){
        echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
        echo "<p>{$row['content']}</p>";
        echo "<a href='edit_post.php?id={$row['id']}'>Edit</a> | ";
        echo "<a href='delete_post.php?id={$row['id']}' class='delete'>Delete</a>";
        echo "<hr>";
    }
    ?>
</body>
</html>