<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("config.php"); // PDO connection

$success = '';
$error = '';

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $number = $_POST['number'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $city = $_POST['city'];
    $year = $_POST['year'];
    $gender = $_POST['gender'];
    $category = $_POST['category'];

    $stmt = $pdo->prepare("INSERT INTO Std_Details (Cust_Name, Cust_Number, Cust_Email, Cust_Course, Cust_City, Cust_Year, Cust_Gender, Cust_Category) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$name, $number, $email, $course, $city, $year, $gender, $category])){
        $cust_id = $pdo->lastInsertId();
        header("Location: followup.php?cust_id=$cust_id");
        exit;
        $success = "Customer data inserted successfully!";
    } else {
        $error = "Operation failed. Please try again.";
    }
}

$customers = $pdo->query("SELECT * FROM Std_Details ORDER BY Cust_Id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Neon CRM - Customer Form</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/82a283d995.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #1e1e2f;
            color: #fff;
            margin: 0;
        }

        .container {
            display: flex;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background: #111126;
            min-height: 100vh;
            padding-top: 20px;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
        }

        #sidebar ul li {
            margin: 20px 0;
        }

        #sidebar ul li a {
            text-decoration: none;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            background: #2e2e50;
        }

        #sidebar .icon {
            width: 30px;
        }

        /* Main */
        .main {
            flex: 1;
            padding: 30px;
            background: #1e1e2f;
        }

        h1, h2 {
            color: #00ffff;
        }

        /* Form Styling */
        form {
            background: #2e2e50;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,255,255,0.2);
            margin-bottom: 40px;
        }

        fieldset {
            border: 1px solid #00ffff;
            margin-bottom: 20px;
            border-radius: 8px;
            padding: 20px;
        }

        legend {
            padding: 0 10px;
            font-weight: bold;
            color: #00ffff;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 500;
        }

        input[type="text"], input[type="number"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            outline: none;
            margin-bottom: 10px;
            font-size: 16px;
            background: #1e1e2f;
            color: #fff;
            border: 1px solid #00ffff;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        button {
            background: #00ffff;
            color: #111126;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #00bfbf;
        }

        .success {
            color: #00ff00;
            margin-bottom: 10px;
        }

        .error {
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #2e2e50;
            border-radius: 12px;
            overflow: hidden;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #00ffff;
        }

        table th {
            background: #111126;
            color: #00ffff;
        }

        table tr:hover {
            background: rgba(0,255,255,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="navigation" id="sidebar">
            <ul>
                <li><a href="index.html"><span class="icon"><i class="fas fa-home"></i></span><span class="title">Dashboard</span></a></li>
                <li><a href="Form.php"><span class="icon"><i class="fas fa-user-tie"></i></span><span class="title">Customer</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <h1>Customer Details Form</h1>
            <?php if($success) echo "<p class='success'>$success</p>"; ?>
            <?php if($error) echo "<p class='error'>$error</p>"; ?>

            <form method="post" action="">
                <fieldset>
                    <legend>General Information</legend>
                    <label>Name:</label>
                    <input type="text" name="name" placeholder="Enter Name" required>

                    <label>Phone:</label>
                    <input type="number" name="number" placeholder="Enter Phone" required>

                    <label>Email:</label>
                    <input type="email" name="email" placeholder="Enter Email" required>

                    <label>Course:</label>
                    <input type="text" name="course" placeholder="Enter Course" required>

                    <label>City:</label>
                    <input type="text" name="city" placeholder="Enter City" required>

                    <label>Year:</label>
                    <input type="text" name="year" placeholder="Enter Year" required>
                </fieldset>

                <fieldset>
                    <legend>Gender</legend>
                    <input type="radio" name="gender" value="Male" required>Male
                    <input type="radio" name="gender" value="Female">Female
                    <input type="radio" name="gender" value="Transgender">Transgender
                </fieldset>

                <fieldset>
                    <legend>Category</legend>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="Hot">Hot</option>
                        <option value="Warm">Warm</option>
                        <option value="Cold">Cold</option>
                    </select>
                </fieldset>

                <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Submit</button>
            </form>

            <h2>Customer Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>City</th>
                        <th>Year</th>
                        <th>Gender</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $customers->fetch()){ ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Cust_Id']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Name']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Number']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Email']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Course']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_City']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Year']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Gender']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Category']) ?></td>
                            <td><a href="followup.php?cust_id=<?= $row['Cust_Id'] ?>" class="followup-btn">
                                <i class="fas fa-plus-circle"></i> Follow-up</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
