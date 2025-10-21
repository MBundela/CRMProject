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

// Handle form submission
if(isset($_POST['mark_attendance'])) {
    $emp_id = $_POST['emp_id'];
    $emp_name = $_POST['emp_name'];
    $attendance_status = $_POST['attendance_status'];
    $leave_status = $_POST['leave_status'];

    // Insert into Employee_Attendance table
    $stmt = $pdo->prepare("INSERT INTO Employee_Attendance (Emp_id, Emp_Name, attendance_status, leave_status, date_marked) VALUES (?, ?, ?, ?, NOW())");
    if($stmt->execute([$emp_id, $emp_name, $attendance_status, $leave_status])){
        $success = "Attendance marked successfully!";
    } else {
        $error = "Failed to mark attendance.";
    }
}

// Fetch existing records
$attendances = $pdo->query("SELECT * FROM Employee_Attendance ORDER BY date_marked DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Attendance - Neon CRM</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    label{display:block;margin:10px 0 5px;font-weight:500;}
    input,select{width:100%;padding:10px;border-radius:8px;border:none;
        outline:none;margin-bottom:10px;font-size:16px;background:#1e1e2f;color:#fff;
        border:1px solid #00ffff;}
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
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <div class="navigation" id="sidebar">
        <ul>
            <li><a href="index.html"><span class="icon"><i class="fas fa-home"></i></span><span class="title">Dashboard</span></a></li>
            <li><a href="attendance.php"><span class="icon"><i class="fas fa-briefcase"></i></span><span class="title">Employee Attendance</span></a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <h1>Employee Attendance</h1>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="post">
            <label>Employee ID</label>
            <input type="text" name="emp_id" required>

            <label>Employee Name</label>
            <input type="text" name="emp_name" required>

            <label>Attendance Status</label>
            <select name="attendance_status" required>
                <option value="">Select Status</option>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Leave">Leave</option>
            </select>

            <label>Leave Type (If Leave)</label>
            <select name="leave_status">
                <option value="">Select Leave Type</option>
                <option value="PL">PL</option>
                <option value="CL">CL</option>
                <option value="SL">SL</option>
                <option value="UL">UL</option>
            </select>

            <button type="submit" name="mark_attendance"><i class="fas fa-paper-plane"></i> Mark Attendance</button>
        </form>

        <h2>Attendance Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Status</th>
                    <th>Leave Type</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($attendances as $att): ?>
                    <tr>
                        <td><?= htmlspecialchars($att['id']); ?></td>
                        <td><?= htmlspecialchars($att['Emp_id']); ?></td>
                        <td><?= htmlspecialchars($att['Emp_Name']); ?></td>
                        <td><?= htmlspecialchars($att['attendance_status']); ?></td>
                        <td><?= htmlspecialchars($att['leave_status']); ?></td>
                        <td><?= htmlspecialchars($att['date_marked']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
