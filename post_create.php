<?php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $content);
        if ($stmt->execute()) {
            header("Location: home.php");
            exit();
        } else {
            $error = "Failed to create post.";
        }
    } else {
        $error = "Post content cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Post</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            background-image: url('uploads/home.png'); /* Add the background image here */
            background-size: cover; /* Ensure the image covers the entire background */
            background-position: center center; /* Center the image */
            background-attachment: scroll;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            color: white; /* Ensures text is visible over background */
        }

        header {
            background-color: rgba(255, 255, 255, 0.8); /* Make header background slightly transparent */
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }

        header a {
            margin-left: 15px;
            text-decoration: none;
            background-color: #b57edc; /* Lavender */
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        header a:hover {
            background-color: #9b59b6;
        }

        .container {
            max-width: 800px;
            margin-top: 80px; /* Ensure it does not overlap the fixed header */
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            opacity: 0.9; /* Slightly transparent to show the background behind */
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2c3e50;
            text-align: center; /* Center the heading */
        }

        .error {
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            resize: vertical;
            box-sizing: border-box;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        button {
            background-color: #7b63e6; /* Lavender */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #6a49b5;
        }

        a {
            text-decoration: none;
            color: #3498db;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 500;
        }

        a:hover {
            color: #2980b9;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</header>

<div class="container">
    <h2>Create a New Post</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="post_create.php" method="POST">
        <textarea name="content" placeholder="What's on your mind?" required></textarea>
        <button type="submit">Post</button>
    </form>

    <div class="form-actions">
        <a href="home.php">⬅ Back to Home</a>
        <a href="profile.php">My Profile</a>
    </div>
</div>

</body>
</html>
