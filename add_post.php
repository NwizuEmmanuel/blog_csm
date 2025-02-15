<?php
include "db.php";
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']){
    die("CSRF validation failed.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $content = htmlspecialchars($content);

    // handle file upload
    if ($_FILES['image']['name']) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target = "upload/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $image_name = NULL;
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, content, image) VALUES (?,?,?)");
    $stmt->bind_param("sss", $title, $content, $image_name);
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
    <link rel="stylesheet" href="style.css">
    <title>Add New Post</title>
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/ae9zzo8jvsnhjx8b12vxazh2kv0sp6ponawym41z8zvwly72/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
        <input type="text" name="title" placeholder="Title" required><br>
        <textarea name="content" required></textarea><br>
        <input type="file" name="image"><br>
        <button type="submit">Add Post</button>
    </form>
    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'autolink lists link image charmap preview',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent'
        });
    </script>
</body>

</html>