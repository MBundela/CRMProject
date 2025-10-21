<?php
include("config.php");

// Fetch latest 5 orders
$stmt = $pdo->query("SELECT Product_Name, Total_Value, Quantity, GST FROM Orders ORDER BY Cust_Id DESC LIMIT 5");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return as JSON
header('Content-Type: application/json');
echo json_encode($orders);
?>
