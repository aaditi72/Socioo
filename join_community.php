<?php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $community_id = $_POST['community_id'];
    $user_id = $_SESSION['user_id'];

    // Insert the user into the community_members table
    $query = "INSERT INTO community_members (user_id, community_id) VALUES ($user_id, $community_id)";
    if ($conn->query($query)) {
        echo "You have successfully joined the community!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
