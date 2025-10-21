<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("config.php"); // PDO connection

$success = '';
$error = '';

// Fetch customers for dropdown
$customers = $pdo->query("SELECT Cust_Id, Cust_Name FROM Std_Details ORDER BY Cust_Name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submit
if (isset($_POST['submit'])) {
    $cust_id = $_POST['cust_id'];
    $visit_date = $_POST['visit_date'];
    $remarks = $_POST['remarks'];

    // Fetch selected customer details
    $stmt = $pdo->prepare("SELECT * FROM Std_Details WHERE Cust_Id = ?");
    $stmt->execute([$cust_id]);
    $cust = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cust) {
        $insert = $pdo->prepare("INSERT INTO Daily_Visits 
            (Visit_Date, Cust_Id, Cust_Name, Cust_Number, Cust_Email, Cust_Course, Cust_City, Cust_Gender, Meeting_Remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($insert->execute([
            $visit_date,
            $cust['Cust_Id'],
            $cust['Cust_Name'],
            $cust['Cust_Number'],
            $cust['Cust_Email'],
            $cust['Cust_Course'],
            $cust['Cust_City'],
            $cust['Cust_Gender'],
            $remarks
        ])) {
            $success = "Visit record added successfully!";
        } else {
            $error = "Failed to add visit record.";
        }
    } else {
        $error = "Customer not found.";
    }
}

// Fetch all visit records
$visits = $pdo->query("SELECT * FROM Daily_Visits ORDER BY Visit_Id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Daily Visits - Neon CRM</title>
<link rel="stylesheet" href="style.css">
<script src="https://kit.fontawesome.com/82a283d995.js" crossorigin="anonymous"></script>
<style>
    body { font-family:'Poppins',sans-serif;background:#1e1e2f;color:#fff;margin:0; }
    .container { display:flex; }
    #sidebar { width:250px;background:#111126;min-height:100vh;padding-top:20px; }
    #sidebar ul{list-style:none;padding:0;}
    #sidebar ul li{margin:20px 0;}
    #sidebar ul li a{text-decoration:none;color:#fff;display:flex;align-items:center;
        padding:10px 20px;border-radius:8px;transition:0.3s;}
    #sidebar ul li a:hover{background:#2e2e50;}
    #sidebar .icon{width:30px;}
    .main{flex:1;padding:30px;background:#1e1e2f;}
    h1,h2{color:#00ffff;}
    form{background:#2e2e50;padding:20px;border-radius:12px;
        box-shadow:0 0 15px rgba(0,255,255,0.2);margin-bottom:40px;}
    fieldset{border:1px solid #00ffff;margin-bottom:20px;border-radius:8px;padding:20px;}
    legend{padding:0 10px;font-weight:bold;color:#00ffff;}
    label{display:block;margin:10px 0 5px;font-weight:500;}
    input,select,textarea{width:100%;padding:10px;border-radius:8px;border:none;
        outline:none;margin-bottom:10px;font-size:16px;background:#1e1e2f;color:#fff;
        border:1px solid #00ffff;}
    input[readonly]{background:#1a1a2b;box-shadow:0 0 10px #00ffff33;}
    button{background:#00ffff;color:#111126;border:none;padding:12px 30px;font-size:16px;
        border-radius:8px;cursor:pointer;transition:0.3s;}
    button:hover{background:#00bfbf;}
    .success{color:#00ff00;margin-bottom:10px;}
    .error{color:#ff4d4d;margin-bottom:10px;}
    table{width:100%;border-collapse:collapse;background:#2e2e50;border-radius:12px;overflow:hidden;}
    table th,td{padding:12px;text-align:left;border-bottom:1px solid #00ffff;}
    table th{background:#111126;color:#00ffff;}
    table tr:hover{background:rgba(0,255,255,0.1);}
</style>
<script>
function fillCustomerDetails() {
    const custId = document.getElementById("cust_id").value;
    if (custId === "") {
        document.getElementById("cust_number").value = '';
        document.getElementById("cust_email").value = '';
        document.getElementById("cust_course").value = '';
        document.getElementById("cust_city").value = '';
        document.getElementById("cust_gender").value = '';
        return;
    }

    // Fetch data from get_customer.php
    fetch(`get_customer.php?cust_id=${custId}`)
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById("cust_number").value = data.Cust_Number || '';
                document.getElementById("cust_email").value = data.Cust_Email || '';
                document.getElementById("cust_course").value = data.Cust_Course || '';
                document.getElementById("cust_city").value = data.Cust_City || '';
                document.getElementById("cust_gender").value = data.Cust_Gender || '';
            }
        })
        .catch(error => console.error("Error fetching customer details:", error));
}
</script>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <div class="navigation" id="sidebar">
        <ul>
            <li><a href="index.html"><span class="icon"><i class="fas fa-home"></i></span><span class="title">Dashboard</span></a></li>
            <li><a href="Form.php"><span class="icon"><i class="fas fa-user-tie"></i></span><span class="title">Customer</span></a></li>
            <li><a href="daily_visits.php"><span class="icon"><i class="fas fa-calendar-check"></i></span><span class="title">Daily Visits</span></a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <h1>Daily Visit Entry</h1>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="post">
            <fieldset>
                <legend>Visit Details</legend>

                <label>Visit Date:</label>
                <input type="date" name="visit_date" required>

                <label>Select Customer:</label>
                <select name="cust_id" id="cust_id" onchange="fillCustomerDetails()" required>
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['Cust_Id'] ?>"><?= htmlspecialchars($c['Cust_Name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Customer Number:</label>
                <input type="text" id="cust_number" readonly>

                <label>Email:</label>
                <input type="text" id="cust_email" readonly>

                <label>Course:</label>
                <input type="text" id="cust_course" readonly>

                <label>City:</label>
                <input type="text" id="cust_city" readonly>

                <label>Gender:</label>
                <input type="text" id="cust_gender" readonly>

                <label>Meeting Remarks:</label>
                <textarea name="remarks" rows="3" placeholder="Enter meeting notes..." required></textarea>

                <button type="submit" name="submit"><i class="fas fa-paper-plane"></i> Save Visit</button>
            </fieldset>
        </form>

        <h2>Visit History</h2>
        <table>
            <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>City</th>
                    <th>Gender</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visits as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['Visit_Id']) ?></td>
                        <td><?= htmlspecialchars($v['Visit_Date']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_Name']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_Number']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_Email']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_Course']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_City']) ?></td>
                        <td><?= htmlspecialchars($v['Cust_Gender']) ?></td>
                        <td><?= htmlspecialchars($v['Meeting_Remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
