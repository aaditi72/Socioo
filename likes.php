<?php
include('db.php');

// Get post ID from URL
$post_id = $_GET['post_id'];

// Fetch the users who liked the post
$query = "
    SELECT users.username
    FROM likes
    JOIN users ON likes.user_id = users.user_id
    WHERE likes.post_id = $post_id
";
$likes = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Likes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="likes-container">
        <h3>Users who liked this post:</h3>
        <?php while ($like = $likes->fetch_assoc()) { ?>
            <p><?php echo $like['username']; ?></p>
        <?php } ?>
        <a href="index.php">Back to Posts</a>
    </div>
</body>
</html>
