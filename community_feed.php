<?php
session_start();
include('db.php');

// Determine sorting option
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; // Default to 'new' if not set

// Define SQL query based on sorting option
switch ($sort) {
    case 'top':
        $order_by = 'ORDER BY vote_score DESC';
        break;
    case 'hot':
        $order_by = 'ORDER BY created_at DESC';
        break;
    case 'trending':
        $order_by = 'ORDER BY (vote_score / DATEDIFF(NOW(), created_at)) DESC';
        break;
    case 'new':
    default:
        $order_by = 'ORDER BY created_at DESC';
        break;
}

$community_id = $_GET['community_id'];
$query = "
    SELECT 
        posts.post_id, 
        posts.content, 
        posts.created_at, 
        users.username,
        users.id AS user_id,
        COALESCE(SUM(CASE WHEN vote_type = 'upvote' THEN 1 
                          WHEN vote_type = 'downvote' THEN -1 
                          ELSE 0 END), 0) AS vote_score,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN post_votes ON posts.post_id = post_votes.post_id
    WHERE posts.community_id = $community_id
    GROUP BY posts.post_id
    $order_by
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Community Feed</title>
    <style>
        /* Styling for sorting links */
        .sort-links {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        .sort-links a {
            text-decoration: none;
            margin-right: 15px;
            color: #937bff;
            font-size: 16px;
        }

        .sort-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="sort-links">
    <a href="community_feed.php?community_id=<?php echo $community_id; ?>&sort=new">New</a>
    <a href="community_feed.php?community_id=<?php echo $community_id; ?>&sort=top">Top</a>
    <a href="community_feed.php?community_id=<?php echo $community_id; ?>&sort=hot">Hot</a>
    <a href="community_feed.php?community_id=<?php echo $community_id; ?>&sort=trending">Trending</a>
</div>

<div class="posts">
    <?php while ($post = $result->fetch_assoc()): ?>
        <div class="post">
            <h4><a href="user_profile.php?user_id=<?php echo $post['user_id']; ?>"><?php echo htmlspecialchars($post['username']); ?></a></h4>
            <p><?php echo htmlspecialchars($post['content']); ?></p>
            <div>
                <span>Score: <?php echo $post['vote_score']; ?></span><br>
                ❤️ <span><?php echo $post['like_count']; ?> Likes</span> |
                💬 <a href="comments.php?post_id=<?php echo $post['post_id']; ?>">Comments: <?php echo $post['comment_count']; ?></a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
