<?php
include("config.php");
header('Content-Type: application/json');

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection not established.");
    }

    $stmt = $pdo->query("SELECT COUNT(*) AS total_visits FROM Daily_Visits");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'totalVisits' => (int)($row['total_visits'] ?? 0)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'totalVisits' => 0,
        'error' => $e->getMessage()
    ]);
}
?>
