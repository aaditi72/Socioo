<?php
session_start();
include('db.php');

// Redirect to login if not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch available communities
$communities_query = "SELECT * FROM communities";
$communities_result = $conn->query($communities_query);

// Filter posts by community if selected
$community_filter = isset($_GET['community_id']) ? $_GET['community_id'] : null;
$community_condition = $community_filter ? "WHERE posts.community_id = $community_filter" : "";

// Fetch posts with user, score, likes, comments, and community
$query = "
    SELECT 
        posts.post_id, 
        posts.content, 
        posts.created_at, 
        users.username,
        users.id AS user_id,
        communities.name AS community_name,
        COALESCE(SUM(CASE WHEN vote_type = 'upvote' THEN 1 
                          WHEN vote_type = 'downvote' THEN -1 
                          ELSE 0 END), 0) AS vote_score,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN communities ON posts.community_id = communities.community_id
    LEFT JOIN post_votes ON posts.post_id = post_votes.post_id
    $community_condition
    GROUP BY posts.post_id
    ORDER BY posts.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-image: url('uploads/home.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .home-container {
            display: flex;
            flex: 1;
            max-width: 100%;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .left {
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 20px;
            width: 200px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .right {
            flex: 1;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            overflow-y: scroll;
        }

        .right h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        /* Enhanced Post Style */
        .post {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .post:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .post h4 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .post p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post span {
            font-size: 14px;
            color: #888;
            margin-right: 10px;
        }

        .post .meta {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 15px;
        }

        /* Voting Buttons */
        .post button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .post button:hover {
            background-color: #0056b3;
        }

        .post .like-comment {
            font-size: 14px;
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
            margin-right: 15px;
        }

        .post .like-comment:hover {
            text-decoration: underline;
        }

        /* Button Alignment */
        .nav-links {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 2;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            background: #937bff;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 14px;
            margin-left: 10px;
        }

        .nav-links a:hover {
            background-color: #7b63e6;
        }

    </style>
</head>
<body>

    <div class="nav-links">
        <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">Profile</a>
        <a href="post_create.php">+ Create Post</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="home-container">
        <div class="left">
            <h1>SOCIOO</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>

        <div class="right">
            <h2>All Posts</h2>

            <!-- Community Filter -->
            <form method="get" action="" style="margin-bottom: 20px;">
                <label for="community">Filter by Community:</label>
                <select name="community_id" id="community">
                    <option value="">All Communities</option>
                    <?php while ($community = $communities_result->fetch_assoc()): ?>
                        <option value="<?php echo $community['community_id']; ?>" <?php echo ($community_filter == $community['community_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($community['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Filter</button>
            </form>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h4><a href="user_profile.php?user_id=<?php echo $row['user_id']; ?>"><?php echo htmlspecialchars($row['username']); ?></a> 
                        <span style="font-size: 14px; color: #888;">(<?php echo htmlspecialchars($row['community_name']); ?>)</span>
                    </h4>
                    <p><?php echo htmlspecialchars($row['content']); ?></p>
                    <div class="meta">
                        <span><?php echo $row['created_at']; ?></span>
                    </div>

                    <!-- Voting buttons -->
                    <div>
                        <form action="vote.php" method="post" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                            <input type="hidden" name="vote_type" value="upvote">
                            <button type="submit">🔼</button>
                        </form>
                        <form action="vote.php" method="post" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?php echo $row['post_id']; ?>">
                            <input type="hidden" name="vote_type" value="downvote">
                            <button type="submit">🔽</button>
                        </form>
                    </div>

                    <div>
                        <span>Score: <?php echo $row['vote_score']; ?></span><br>
                        ❤️ <span><?php echo $row['like_count']; ?> Likes</span> |
                        💬 <a href="comments.php?post_id=<?php echo $row['post_id']; ?>" class="like-comment">Comments: <?php echo $row['comment_count']; ?></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
