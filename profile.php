<?php
session_start();
include('db.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Use session user_id for the logged-in user
$query = "SELECT username, email FROM users WHERE id = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

// Query for posts
$posts_query = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";
$posts_result = $conn->query($posts_query);

// Query for follower count
$follower_query = "SELECT COUNT(*) AS follower_count FROM followers WHERE following_id = '$user_id'";
$follower_result = $conn->query($follower_query);
$follower_data = $follower_result->fetch_assoc();
$follower_count = $follower_data['follower_count'];

// Query for following count
$following_query = "SELECT COUNT(*) AS following_count FROM followers WHERE follower_id = '$user_id'";
$following_result = $conn->query($following_query);
$following_data = $following_result->fetch_assoc();
$following_count = $following_data['following_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    
    <style>
        * {
            box-sizing: border-box;
        }

       body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-image: url('uploads/home.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}



        header {
            background-color: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

       header a {
    margin-left: 15px;
    text-decoration: none;
    background-color: #7b63e6 /* Lavender */
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-info h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .profile-info p {
            font-size: 18px;
            color: #555;
        }

        .posts-section {
            margin-top: 30px;
        }

        .posts-section h3 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .post {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .post h4 {
            margin: 0 0 10px;
            color: #2c3e50;
        }

        .post p {
            margin: 5px 0;
            color: #34495e;
        }

        .post .date {
            font-size: 13px;
            color: gray;
        }
    </style>

</head>
<body>

<header>
    <a href="home.php">Back to Home</a>
    <a href="edit_profile.php">Edit Profile</a>
    <a href="logout.php">Logout</a>
</header>

<div class="container">
    <div class="profile-info">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
        <!-- Display follower and following counts -->
        <div class="stats">
            <p>
                <strong>Followers:</strong> 
                <a href="followers_list.php?user_id=<?php echo $user_id; ?>"><?php echo $follower_count; ?> followers</a>
            </p>
            <p>
                <strong>Following:</strong> 
                <a href="following_list.php?user_id=<?php echo $user_id; ?>"><?php echo $following_count; ?> following</a>
            </p>
        </div>
    </div>

    <div class="posts-section">
        <h3>Your Posts</h3>
        <?php
        if ($posts_result->num_rows > 0) {
            while ($post = $posts_result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h4>" . htmlspecialchars($user['username']) . "</h4>";
                echo "<p>" . htmlspecialchars($post['content']) . "</p>";
                echo "<p class='date'>Posted on: " . $post['created_at'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No posts available.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
