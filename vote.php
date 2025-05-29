<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$vote_type = $_POST['vote_type'];

if (!in_array($vote_type, ['upvote', 'downvote'])) {
    die("Invalid vote type");
}

// Check if vote already exists
$check = $conn->prepare("SELECT * FROM post_votes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE post_votes SET vote_type = ? WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("sii", $vote_type, $user_id, $post_id);
} else {
    $stmt = $conn->prepare("INSERT INTO post_votes (user_id, post_id, vote_type) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $post_id, $vote_type);
}
$stmt->execute();

header("Location: home.php");
exit();
?>
