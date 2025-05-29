<?php include 'db.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $content);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Post</title>
</head>
<body>
    <h2>Create New Post</h2>
    <form method="post">
        <label>User ID:</label><br>
        <input type="number" name="user_id" required><br><br>

        <label>Content:</label><br>
        <textarea name="content" rows="4" cols="40" required></textarea><br><br>

        <input type="submit" value="Post">
    </form>
    <br>
    <a href="index.php">← Back to posts</a>
</body>
</html>
