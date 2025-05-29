<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$profile_id = $_GET['user_id'];

// Get profile user info
$query = "SELECT username, email FROM users WHERE id = $profile_id";
$user_result = $conn->query($query);
$user = $user_result->fetch_assoc();

// Check if already following
$isFollowing = false;
$stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $user_id, $profile_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $isFollowing = true;
}
$stmt->close();

// Handle follow/unfollow
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow'])) {
        $stmt = $conn->prepare("INSERT INTO followers (follower_id, following_id, followed_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $profile_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['unfollow'])) {
        $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $user_id, $profile_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: user_profile.php?user_id=" . $profile_id);
    exit();
}

// Count followers
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM followers WHERE following_id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();
$followers_count = $result->fetch_assoc()['count'];
$stmt->close();

// Count following
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM followers WHERE follower_id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();
$following_count = $result->fetch_assoc()['count'];
$stmt->close();

// Get user posts
$post_query = "
    SELECT 
        posts.post_id, 
        posts.content, 
        posts.created_at, 
        COALESCE(SUM(CASE WHEN vote_type = 'upvote' THEN 1 
                          WHEN vote_type = 'downvote' THEN -1 
                          ELSE 0 END), 0) AS vote_score,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
    FROM posts
    LEFT JOIN post_votes ON posts.post_id = post_votes.post_id
    WHERE posts.user_id = $profile_id
    GROUP BY posts.post_id
    ORDER BY posts.created_at DESC
";

$posts_result = $conn->query($post_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f3e5f5;
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
        background-color: #b57edc;
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
        margin: 30px auto;
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .post {
        background-color: #e6d5f7;
        border: 1px solid #d2b4f2;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .comment {
        background-color: #f9f2ff;
        border-left: 4px solid #9370DB;
        padding: 10px;
        margin: 10px 0 10px 20px;
        border-radius: 5px;
    }

    textarea {
        width: 100%;
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .comment-button {
        background-color: #b57edc;
        color: white;
        padding: 8px 16px;
        border: none;
        margin-top: 5px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .comment-button:hover {
        background-color: #9b59b6;
    }

    .follow-form {
        margin: 10px 0;
    }

    .follow-form button {
        background-color: #b57edc;
        color: white;
        padding: 6px 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .follow-form button:hover {
        background-color: #9b59b6;
    }
    </style>
</head>
<body>

<header>
    <a href="home.php">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</header>

<div class="container profile-container">
    <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>

    <div class="profile-details">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Total Posts:</strong> <?php echo $posts_result->num_rows; ?></p>
        <p><strong>Followers:</strong> <?php echo $followers_count; ?> |
           <strong>Following:</strong> <?php echo $following_count; ?></p>

        <?php if ($user_id != $profile_id): ?>
            <form method="POST" class="follow-form">
                <?php if ($isFollowing): ?>
                    <button type="submit" name="unfollow">Unfollow</button>
                <?php else: ?>
                    <button type="submit" name="follow">Follow</button>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>

    <h3>Posts by <?php echo htmlspecialchars($user['username']); ?></h3>

    <?php while ($post = $posts_result->fetch_assoc()): ?>
        <div class="post">
            <p><strong><?php echo htmlspecialchars($user['username']); ?></strong> | <?php echo $post['created_at']; ?></p>
            <p><?php echo htmlspecialchars($post['content']); ?></p>
            <p><em>Score:</em> <?php echo $post['vote_score']; ?> |
               ❤️ <?php echo $post['like_count']; ?> |
               💬 <?php echo $post['comment_count']; ?></p>

            <h4>Comments:</h4>
            <?php
            $comment_query = "
                SELECT comments.content, comments.created_at, users.username
                FROM comments
                JOIN users ON comments.user_id = users.id
                WHERE comments.post_id = " . $post['post_id'] . "
                ORDER BY comments.created_at ASC
            ";
            $comments_result = $conn->query($comment_query);
            while ($comment = $comments_result->fetch_assoc()):
            ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> | <?php echo $comment['created_at']; ?></p>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                </div>
            <?php endwhile; ?>

            <form action="add_comments.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                <textarea name="content" placeholder="Add a comment..." required></textarea>
                <button type="submit" class="comment-button">Comment</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
