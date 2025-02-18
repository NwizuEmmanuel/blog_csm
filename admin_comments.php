<?php
include 'db.php';
session_start();

// check user is admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Access denied");
}

// fetch pending comments
try {
    $result = $conn->query("
SELECT comments.id, comments.content, comments.status, posts.title
FROM comments
JOIN posts ON comments.post_id = posts.id
WHERE comments.status = 'pending'
");

echo "<h2>Pending Comments</h2>";
while ($row = $result->fetch_assoc()){
    echo "<p><strong>Post:</strong> {$row['title']}</p>";
    echo "<p><strong>Comment:</strong> {$row['content']}</p>";
    echo "<a href='approve_comment.php?id={$row['id']}'>Approve</a> | ";
    echo "<a href='delete_comment.php?id={$row['id']}'>Delete</a>";
    echo "<hr>";
}

echo "<hr>";
echo "<a href='index.php'>Go back to posts.</a>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
