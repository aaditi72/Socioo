<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

    // Execute the query
    if ($conn->query($sql)) {
        header("Location: index.php"); // Redirect to login page after successful registration
        exit();
    } else {
        echo "<script>alert('Registration failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
       body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    background-image: url('uploads/register.jpg'); 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}



.register-container {
    display: flex;
    width: 80%;
    max-width: 1000px;
    height: 600px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    background-color: white;
}

        .left, .right {
            flex: 1;
            padding: 40px;
        }

        .left {
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .left input {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border: none;
            border-bottom: 1px solid #ccc;
            outline: none;
            background: transparent;
        }

        .left button {
            padding: 10px;
            width: 100%;
            background-color: #a18bfa;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
        }

        .left p {
            margin-top: 15px;
        }

        .left a {
            color: #6c5ce7;
            text-decoration: none;
            font-weight: bold;
        }

        .right {
            background: url('uploads/register.jpg') no-repeat center center/cover;
            color: white;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            max-width: 80%;
        }

        .overlay h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .overlay p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .overlay .login-link {
            padding: 10px 20px;
            background-color: white;
            color: #6c5ce7;
            font-weight: bold;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="left">
        <h2>Register</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
    <div class="right">
        <div class="overlay">
            <h1>Socioo</h1>
            <p>It's a digital space where voices can be amplified, stories can go viral, and communities can grow across borders.</p>
            <a class="login-link" href="index.php">Login</a>
        </div>
    </div>
</div>
</body>
</html>
