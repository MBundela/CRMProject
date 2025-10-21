<?php
include("config.php"); // make sure this defines $pdo
header('Content-Type: application/json');

try {
    // Count all customers
    $stmt = $pdo->query("SELECT COUNT(*) AS total_customers FROM Std_Details");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalCustomers = $row ? (int)$row['total_customers'] : 0;

    echo json_encode(['totalCustomers' => $totalCustomers]);
} catch (Exception $e) {
    echo json_encode(['totalCustomers' => 0, 'error' => $e->getMessage()]);
}
?>
