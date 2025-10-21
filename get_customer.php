<?php
include("config.php"); // ensure $pdo is valid

if(isset($_GET['cust_id'])){
    $cust_id = $_GET['cust_id'];

    $stmt = $pdo->prepare("SELECT Cust_Number, Cust_Email, Cust_Course, Cust_City, Cust_Gender 
                           FROM Std_Details WHERE Cust_Id = ?");
    $stmt->execute([$cust_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data ?: []);
}
?>
