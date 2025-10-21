<?php
session_start();
include 'config.php';
$error = '';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.html");
        exit;
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
/* Same neon styling as registration page */
body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #0f0f0f;
    font-family: 'Poppins', sans-serif;
    color: #fff;
}
.container {
    background: rgba(0,0,0,0.7);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 20px #ff00ff;
    text-align: center;
    width: 350px;
}
.container h2 {
    margin-bottom: 20px;
    color: #ff00ff;
    text-shadow: 0 0 10px #ff00ff;
}
input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    background: transparent;
    border: 2px solid #ff00ff;
    border-radius: 5px;
    color: #ff00ff;
    font-size: 16px;
}
input:focus {
    border-color: #0ff;
    box-shadow: 0 0 10px #0ff;
    outline: none;
}
button {
    width: 100%;
    padding: 10px;
    background: #ff00ff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}
button:hover {
    background: #0ff;
    color: #000;
    box-shadow: 0 0 20px #0ff;
}
.error { color: #f00; margin: 10px 0; }
</style>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
    <p style="margin-top: 15px;">Don't have an account? <a href="register.php" style="color:#0ff;">Register</a></p>
</div>
</body>
</html>
