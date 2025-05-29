<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get followers
$sql = "
    SELECT u.id, u.username 
    FROM followers f 
    JOIN users u ON f.follower_id = u.id 
    WHERE f.following_id = $user_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Followers</title>
    <link rel="stylesheet" href="style.css">
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
        background-color: #7b63e6;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background 0.3s ease;
    }

    header a:hover {
        background-color: #5a49c4;
    }

    .container {
        max-width: 800px;
        margin: 40px auto;
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #2c3e50;
    }

    ul {
        list-style: none;
        padding: 0;
        margin-top: 30px;
    }

    li {
        background-color: #ecf0f1;
        margin-bottom: 10px;
        padding: 15px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    li:hover {
        background-color: #dfe6e9;
    }

    li a {
        color: #34495e;
        text-decoration: none;
        font-weight: bold;
    }

    li a:hover {
        text-decoration: underline;
    }

    .back-button {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        background-color: #7b63e6;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .back-button:hover {
        background-color: #5a49c4;
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
    <h2>Your Followers</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><a href="user_profile.php?user_id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></a></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>You have no followers yet.</p>
    <?php endif; ?>
    <a class="back-button" href="profile.php">← Back to Profile</a>
</div>

</body>
</html>
