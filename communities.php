<?php
session_start();
include('db.php');

// Fetch communities
$query = "SELECT * FROM communities";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Communities</title>
    <style>
        /* Add any CSS for community display */
    </style>
</head>
<body>

    <h2>Available Communities</h2>
    
    <?php while ($community = $result->fetch_assoc()): ?>
        <div>
            <h3><?php echo htmlspecialchars($community['name']); ?></h3>
            <p><?php echo htmlspecialchars($community['description']); ?></p>
            <form action="join_community.php" method="post">
                <input type="hidden" name="community_id" value="<?php echo $community['community_id']; ?>">
                <button type="submit">Join</button>
            </form>
        </div>
    <?php endwhile; ?>

</body>
</html>
