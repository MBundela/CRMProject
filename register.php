<?php
include 'config.php';
$success = '';
$error = '';

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? OR username=?");
    $stmt->execute([$email, $username]);
    if($stmt->rowCount() > 0){
        $error = "Username or Email already exists!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
        if($stmt->execute([$username, $email, $password])){
            $success = "Registration Successful! <a href='login.php'>Login Here</a>";
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
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
    box-shadow: 0 0 20px #0ff, 0 0 40px #ff00ff;
    text-align: center;
    width: 350px;
    animation: fadeIn 1s ease;
}
@keyframes fadeIn {
    0% {opacity:0; transform: translateY(-20px);}
    100% {opacity:1; transform: translateY(0);}
}
.container h2 {
    margin-bottom: 20px;
    color: #0ff;
    text-shadow: 0 0 10px #0ff, 0 0 20px #ff00ff;
}
input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    background: transparent;
    border: 2px solid #0ff;
    border-radius: 5px;
    color: #0ff;
    font-size: 16px;
    transition: 0.3s;
}
input:focus {
    border-color: #ff00ff;
    box-shadow: 0 0 10px #ff00ff, 0 0 20px #0ff;
    outline: none;
}
button {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    background: #0ff;
    color: #000;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: 0.3s;
}
button:hover {
    background: #ff00ff;
    color: #fff;
    box-shadow: 0 0 20px #ff00ff, 0 0 40px #0ff;
}

/* Direct login button styling */
.login-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    color: #0ff;
    border: 2px solid #0ff;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.login-btn:hover {
    color: #000;
    background: #ff00ff;
    box-shadow: 0 0 15px #ff00ff, 0 0 30px #0ff;
}
.success { color: #0f0; margin: 10px 0; }
.error { color: #f00; margin: 10px 0; }
</style>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>
    <a href="login.php" class="login-btn">Already have an account? Login</a>
</div>
</body>
</html>
