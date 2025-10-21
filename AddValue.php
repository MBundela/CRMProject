<?php
include("config.php");

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT SUM(Total_Value) AS total FROM Orders");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalSales = $row && $row['total'] !== null ? (float)$row['total'] : 0;

    echo json_encode(['totalSales' => $totalSales]);
} catch (Exception $e) {
    echo json_encode(['totalSales' => 0, 'error' => $e->getMessage()]);
}
?>
