<?php
include "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']){
        die("CSRF validation failed.");
    }

    $title = htmlspecialchars($_POST["title"], ENT_QUOTES, 'UTF-8');
    $content = $_POST["content"];

    // handle file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $upload_dir = "uploads/";

        // ensure upload directory exists
        if (!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }

        // Extract file info
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // validate file extension
        if (!in_array($file_ext, $allowed_extensions)){
            die("Error: Invalid file type. Allowed: " . implode(", ", $allowed_extensions));
        }

        // validate file size (limit: 5MB)
        if ($file_size > 5 * 1024 * 1024){
            die("Error: File is too large. Max 5MB allowed.");
        }

        // generate unique filename
        $image_name = time() . "_" . uniqid() . "." . $file_ext;
        $target = $upload_dir . $image_name;

        // move file to upload dircetory
        if(!move_uploaded_file($file_tmp, $target)){
            die("Error uploading file");
        }
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
    <form method="post" enctype="multipart/form-data">
        <?php $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
        <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
        <input type="text" name="title" placeholder="Title" required><br>
        <textarea name="content" id="content" required></textarea><br>
        <input type="file" name="image"><br>
        <button type="submit">Add Post</button>
    </form>
    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
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