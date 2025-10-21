<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include("config.php"); // ensure $pdo is a valid PDO connection

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $Cust_Id = $_POST['Cust_Id'];
    $Cust_Name = $_POST['Cust_Name'];
    $Father_Name = $_POST['Father_Name'];
    $City = $_POST['City'];
    $Product_Name = $_POST['Product_Name'];
    $Quantity = $_POST['Quantity'];
    $Order_Value = $_POST['Order_Value'];

    // Calculate total with 18% GST
    $total_value = $Order_Value * $Quantity;
    $gst = $total_value * 0.18;
    $total_with_gst = $total_value + $gst;

    // Insert record into Orders table
    $stmt = $pdo->prepare("INSERT INTO Orders (Cust_Id, Cust_Name, Father_Name, City, Product_Name, Quantity, Order_Value, GST, Total_Value) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$Cust_Id, $Cust_Name, $Father_Name, $City, $Product_Name, $Quantity, $Order_Value, $gst, $total_with_gst])) {
        $success = "✅ Order added successfully!";
    } else {
        $error = "❌ Operation failed. Please try again.";
    }
}

// Fetch all orders
$orders = $pdo->query("SELECT * FROM Orders ORDER BY Cust_Id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Neon CRM - Orders</title>
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
        input[type="text"], input[type="number"] {
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
                <li><a href="orders.php"><span class="icon"><i class="fas fa-shopping-cart"></i></span><span class="title">Orders</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main">
            <h1>Add Order</h1>
            <?php if($success) echo "<p class='success'>$success</p>"; ?>
            <?php if($error) echo "<p class='error'>$error</p>"; ?>

            <form method="post" action="">
                <fieldset>
                    <legend>Order Information</legend>
                    <label>Customer ID:</label>
                    <input type="number" name="Cust_Id" placeholder="Enter Customer ID" required>

                    <label>Customer Name:</label>
                    <input type="text" name="Cust_Name" placeholder="Enter Name" required>

                    <label>Father's Name:</label>
                    <input type="text" name="Father_Name" placeholder="Enter Father's Name" required>

                    <label>City:</label>
                    <input type="text" name="City" placeholder="Enter City" required>

                    <label>Product Name:</label>
                    <input type="text" name="Product_Name" placeholder="Enter Product Name" required>

                    <label>Item Quantity:</label>
                    <input type="number" name="Quantity" placeholder="Enter Quantity" required>

                    <label>Order Value (per item):</label>
                    <input type="number" name="Order_Value" placeholder="Enter Value" required>
                </fieldset>

                <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Submit</button>
            </form>

            <h2>Order Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Cust_Id</th>
                        <th>Cust_Name</th>
                        <th>Father_Name</th>
                        <th>City</th>
                        <th>Product_Name</th>
                        <th>Quantity</th>
                        <th>Order_Value</th>
                        <th>GST (18%)</th>
                        <th>Total_Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $orders->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Cust_Id']) ?></td>
                            <td><?= htmlspecialchars($row['Cust_Name']) ?></td>
                            <td><?= htmlspecialchars($row['Father_Name']) ?></td>
                            <td><?= htmlspecialchars($row['City']) ?></td>
                            <td><?= htmlspecialchars($row['Product_Name']) ?></td>
                            <td><?= htmlspecialchars($row['Quantity']) ?></td>
                            <td><?= htmlspecialchars($row['Order_Value']) ?></td>
                            <td><?= htmlspecialchars($row['GST']) ?></td>
                            <td><?= htmlspecialchars($row['Total_Value']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
