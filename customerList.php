<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("./config.php");
$stmt = $conn->query("SELECT * FROM Std_Details ORDER BY Std_Id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Records</title>
    <link rel="stylesheet" href="CSS/Input.css">
</head>
<body>
    <h2>Customer List</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th><th>Name</th><th>Number</th><th>Email</th>
            <th>Course</th><th>City</th><th>Year</th><th>Gender</th><th>Category</th>
        </tr>
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
        <tr>
            <td><?= $row['Std_Id'] ?></td>
            <td><?= $row['Std_Name'] ?></td>
            <td><?= $row['Std_Number'] ?></td>
            <td><?= $row['Std_Email'] ?></td>
            <td><?= $row['Std_Course'] ?></td>
            <td><?= $row['Std_City'] ?></td>
            <td><?= $row['Std_Year'] ?></td>
            <td><?= $row['Std_Gender'] ?></td>
            <td><?= $row['Std_Category'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
