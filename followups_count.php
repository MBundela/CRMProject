<?php
include("config.php");
header('Content-Type: application/json');

try {
    // Count follow-ups where Next_Followup_Date >= today (pending ones)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM Followups
        WHERE DATE(Next_Followup_Date) >= CURDATE()
    ");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'totalFollowups' => $row ? (int)$row['total'] : 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
