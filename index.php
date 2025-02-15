<?php include "db.php";
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$result = $conn->query("SELECT role FROM users WHERE username = '$user'");
$row = $result->fetch_assoc();
$role = $row["role"];

if ($role === "admin") {
    echo "<a href='add_post.php'>Add New Post</a>";
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
    <h2>All Posts</h2>
    <?php
    $limit = 1;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    $total_posts = $conn->query("SELECT COUNT(*) AS count FROM posts")->fetch_assoc()["count"];
    $total_pages = ceil($total_posts / $limit);

    ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php if ($row['image']): ?>
            <?= "<img src='uploads/{$row['image']}' width='200'>"; ?>
        <?php endif ?>
        <?= "<h3>" . htmlspecialchars($row['title']) . "</h3>"; ?>
        <?= "<p>{$row['content']}</p>"; ?>
        <?php if ($role === "admin") : ?>
            <?= "<a href='edit_post.php?id={$row['id']}'>Edit</a> | "; ?>
            <?= "<a href='delete_post.php?id={$row['id']}' class='delete'>Delete</a>"; ?>
        <?php endif ?>
        <form action="add_comment.php" method="post">
            <input type="hidden" name="post_id" value="<?= $row['id']; ?>">
            <textarea name="content" id="content" placeholder="Add a comment..." required></textarea><br>
            <button type="submit">Comment</button>
        </form>
        <?php
        $comments = $conn->query("SELECT * FROM comments WHERE post_id = {$row['id']}");
        while ($comment = $comments->fetch_assoc()) {
            $user_result = $conn->query("SELECT id,username FROM users WHERE id = {$comment['user_id']}");
            $username = $user_result->fetch_assoc();
            $username = $username["username"];
            if ($username === $user) {
                $username = "me";
            }
            echo "<p><strong>{$username}: </strong> {$comment['content']}</p>";
        }
        ?>
        <?= "<hr>"; ?>
    <?php endwhile ?>
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="index.php?page=<?=$i?>" class="paginator"><?=$i ?></a>
        <?php endfor ?>
    </div>
</body>

</html>