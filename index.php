<?php include "db.php";
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

if ($role === "admin") {
    echo "<a href='add_post.php'>Add New Post</a> | ";
    echo "<a href='admin_comments.php'>Manage comments</a>";
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
    $limit = 5;
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
        <div>
            <?php echo $row['content']; ?>
        </div>
        <?php if ($role === "admin") : ?>
            <?= "<a href='edit_post.php?id={$row['id']}'>Edit</a> | "; ?>
            <?= "<a href='delete_post.php?id={$row['id']}' class='delete'>Delete</a>"; ?>
        <?php endif ?>
        <form action="add_comment.php" method="post">
            <input type="hidden" name="post_id" value="<?= $row['id']; ?>">
            <input type="hidden" name="parent_id" value="0">
            <textarea name="content" placeholder="Write a comment..." required></textarea><br>
            <button type="submit">Post Comment</button>
        </form>

        <?php
        $comments = $conn->query("SELECT * FROM comments WHERE post_id = {$row['id']} AND status='approved'");
        ?>
        <?php while ($comment = $comments->fetch_assoc()) : ?>
            <?php $user_result = $conn->query("SELECT id,username FROM users WHERE id = {$comment['user_id']}"); ?>
            <?php $username = $user_result->fetch_assoc(); ?>
            <?php $username = $username["username"]; ?>
            <div>
                <span><?= $comment['total_likes'] . " likes"?></span>
                <span><?= $comment['total_dislike'] . " dislikes" ?></span>
            </div>
            <?php echo "<p><strong>{$username}: </strong> {$comment['content']}</p>"; ?>
            <a href="like_comment.php?id=<?= $comment["id"] ?>&parent_id=<?= $comment["parent_id"] ?>">Like</a>
            <a href="dislike_comment.php?id=<?= $comment["id"] ?>&parent_id=<?= $comment["parent_id"] ?>">Dislike</a>
            <!-- reply button -->
            <form action="add_comment.php" method="post">
                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                <textarea name="content" placeholder="Reply to this comment..." required></textarea><br>
                <button type="submit">Reply</button>
            </form>
            <?php $replies = $conn->query("SELECT * FROM comments WHERE parent_id = {$comment['id']} AND status = 'approved'"); ?>
            <?php while ($reply = $replies->fetch_assoc()) : ?>
                <div style="margin-left:30px;">
                    <p>
                        <strong>Reply: </strong><?= $reply['content'] ?>
                    </p>
                </div>
            <?php endwhile ?>
        <?php endwhile ?>
        <?= "<hr>"; ?>
    <?php endwhile ?>
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="index.php?page=<?= $i ?>" class="paginator"><?= $i ?></a>
        <?php endfor ?>
    </div>
</body>

</html>