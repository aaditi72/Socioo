<?php
session_start();
include('db.php');

if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SOCIOO - Login</title>
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
    align-items: center;
    justify-content: center;

    background-image: url('uploads/login.jpg'); 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

    .container {
    display: flex;
    width: 80%;
    max-width: 1000px;
    height: 600px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    background-color: white;
    }

    .left {
      background: url('uploads/login.jpg') no-repeat center center/cover;
      color: white;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 50px;
      position: relative;
    }

    .left::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .left-content {
      position: relative;
      z-index: 2;
    }

    .left h1 {
      font-size: 50px;
      font-weight: bold;
    }

    .left p {
      margin: 20px 0 10px;
    }

    .left a {
      display: inline-block;
      margin-top: 10px;
      background: white;
      color: #5e4fd3;
      padding: 10px 20px;
      text-decoration: none;
      font-weight: bold;
      border-radius: 5px;
    }

    .right {
      flex: 1;
      padding: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .right h2 {
      margin-bottom: 30px;
      color: #333;
    }

    .input-group {
      margin-bottom: 20px;
    }

    .input-group label {
      display: block;
      margin-bottom: 5px;
      color: #555;
    }

    .input-group input {
      width: 100%;
      padding: 10px;
      border: none;
      border-bottom: 2px solid #ccc;
      font-size: 16px;
      background: transparent;
    }

    .input-group input:focus {
      outline: none;
      border-color: #937bff;
    }

    .login-btn {
      background-color: #937bff;
      border: none;
      color: white;
      padding: 12px 20px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      border-radius: 5px;
      margin-top: 10px;
    }

    .login-btn:hover {
      background-color: #7b63e6;
    }

    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="left">
    <div class="left-content">
      <h1>SOCIOO</h1>
      <p>Welcome back! Please login to your account.</p>
      <p>Don't have an account?</p>
      <a href="register.php">Register</a>
    </div>
  </div>
  <div class="right">
    <h2>Login</h2>
    <form method="POST" action="index.php">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>
      <button class="login-btn" type="submit">Login</button>
      <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
    </form>
  </div>
</div>

</body>
</html>

