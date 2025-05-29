<?php
session_start();
include('db.php');

// Check if post_id is provided in the URL
if (!isset($_GET['post_id'])) {
    die("Post not found.");
}

$post_id = $_GET['post_id'];

// Fetch post details
$query = "
    SELECT posts.post_id, posts.content, posts.created_at, users.username
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.post_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Comments</title>
    <style>
        /* Comment Section Styles */
        .comment {
            padding: 15px;
            background-color: #f9f9f9;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .comment p {
            font-size: 14px;
            color: #555;
        }

        .comment strong {
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: vertical;
        }

        button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }
        
        h2, h3 {
            color: #333;
        }
        
        p {
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Post by <?php echo htmlspecialchars($post['username']); ?></h2>
    <p><?php echo htmlspecialchars($post['content']); ?></p>
    <p style="font-size: 12px; color: gray;"><?php echo $post['created_at']; ?></p>

    <h3>Comments</h3>

    <?php
    // Fetch comments for the post
    $query = "
        SELECT comments.content, comments.created_at, users.username
        FROM comments
        JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()):
    ?>
        <div class="comment">
            <p><strong><?php echo htmlspecialchars($row['username']); ?>:</strong></p>
            <p><?php echo htmlspecialchars($row['content']); ?></p>
            <p style="font-size: 12px; color: gray;"><?php echo $row['created_at']; ?></p>
        </div>
    <?php endwhile; ?>

    <!-- Comment submission form -->
    <h4>Add a Comment</h4>
    <form action="add_comments.php" method="post">
        <textarea name="content" placeholder="Write your comment here..." required></textarea><br>
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <button type="submit">Submit Comment</button>
    </form>
</div>
</body>
</html>

