<?php
include "db.php";
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['csrf_token'] !== $_POST['csrf_token']){
        die("CSRF validation failed.");
    }
    $id = $_POST["id"];
    $title = htmlspecialchars($_POST['title']);
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE posts SET title=?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tiny.cloud/1/ae9zzo8jvsnhjx8b12vxazh2kv0sp6ponawym41z8zvwly72/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
    <div class="container">
        <form method="POST">
            <?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
            <input type="hidden" value="<?= $_SESSION['csrf_token']?>" name='csrf_token'>
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="text" name="title" required placeholder="Post Title" value="<?= $post['title'] ?>">
            <textarea name="content" id="content" required placeholder="Post Content"><?= $post['content'] ?></textarea>
            <button type="submit">Update Post</button>
        </form>
    </div>
    <script>
        tinymce.init({
            selector: 'textarea#content',
            plugins: 'autolink lists link image charmap preview',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            setup: function(editor){
                editor.on('change', function(){
                    tinymce.triggerSave();
                });
            }
        });
    </script>
</body>

</html>