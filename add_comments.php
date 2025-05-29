<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'], $_POST['post_id'])) {
    $comment_text = $_POST['content'];
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (!empty($comment_text)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
        
        if ($stmt->execute()) {
            // Redirect to the post page after adding the comment
            header("Location: comments.php?post_id=" . $post_id);
            exit();
        } else {
            echo "<script>alert('Error adding comment. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Comment cannot be empty.');</script>";
    }
}
?>
