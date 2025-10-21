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

// ===== Handle Add / Edit / Delete =====

// ADD Follow-up
if (isset($_POST['add_followup'])) {
    $cust_id = $_POST['cust_id'];
    $followup_date = $_POST['followup_date'];
    $next_followup_date = $_POST['next_followup_date'];
    $reason = $_POST['reason'];

    $stmt = $pdo->prepare("SELECT * FROM Std_Details WHERE Cust_Id = ?");
    $stmt->execute([$cust_id]);
    $cust = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cust) {
        $insert = $pdo->prepare("INSERT INTO Followups 
            (Followup_Date, Cust_Id, Cust_Name, Cust_Number, Cust_Email, Next_Followup_Date, Cust_City, Cust_Gender, Followup_Reason)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($insert->execute([
            $followup_date,
            $cust['Cust_Id'],
            $cust['Cust_Name'],
            $cust['Cust_Number'],
            $cust['Cust_Email'],
            $next_followup_date,
            $cust['Cust_City'],
            $cust['Cust_Gender'],
            $reason
        ])) {
            $success = "✅ Follow-up added successfully!";
        } else $error = "❌ Failed to save follow-up.";
    } else $error = "❌ Customer not found.";
}

// EDIT Follow-up
if (isset($_POST['update_followup'])) {
    $id = $_POST['followup_id'];
    $next_date = $_POST['edit_next_followup_date'];
    $reason = $_POST['edit_reason'];

    $update = $pdo->prepare("UPDATE Followups SET Next_Followup_Date=?, Followup_Reason=? WHERE Followup_Id=?");
    if ($update->execute([$next_date, $reason, $id])) {
        $success = "✅ Follow-up updated successfully!";
    } else $error = "❌ Update failed.";
}

// DELETE Follow-up
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM Followups WHERE Followup_Id=?")->execute([$id]);
    $success = "🗑️ Follow-up deleted successfully!";
}

// Fetch all followups
$followups = $pdo->query("SELECT * FROM Followups ORDER BY Followup_Id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Follow-ups - Neon CRM</title>
<script src="https://kit.fontawesome.com/82a283d995.js" crossorigin="anonymous"></script>
<style>
body { font-family:'Poppins',sans-serif;background:#1e1e2f;color:#fff;margin:0; }
.container { display:flex; min-height:100vh; }
#sidebar { width:250px;background:#111126;padding-top:20px; }
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
button{background:#00ffff;color:#111126;border:none;padding:10px 20px;font-size:15px;
    border-radius:8px;cursor:pointer;transition:0.3s;margin-top:10px;}
button:hover{background:#00bfbf;}
.success{color:#00ff00;margin-bottom:10px;}
.error{color:#ff4d4d;margin-bottom:10px;}
table{width:100%;border-collapse:collapse;background:#2e2e50;border-radius:12px;overflow:hidden;}
table th,td{padding:10px;text-align:left;border-bottom:1px solid #00ffff;}
table th{background:#111126;color:#00ffff;}
table tr:hover{background:rgba(0,255,255,0.1);}
.action-btns a{color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;margin-right:5px;}
.edit-btn{background:#00bfbf;}
.delete-btn{background:#ff4d4d;}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.8);justify-content:center;align-items:center;z-index:999;}
.modal-content{background:#2e2e50;padding:30px;border-radius:12px;min-width:400px;
    box-shadow:0 0 15px #00ffffaa;position:relative;}
.close-btn{position:absolute;top:10px;right:15px;color:#fff;font-size:20px;cursor:pointer;}
.close-btn:hover{color:#00ffff;}
</style>

<script>
function fillCustomerDetails() {
    const custId = document.getElementById("cust_id").value;
    if (custId === "") {
        ["cust_number","cust_email","cust_city","cust_gender"].forEach(id => document.getElementById(id).value='');
        return;
    }

    fetch(`get_customer.php?cust_id=${custId}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                document.getElementById("cust_number").value = data.Cust_Number || '';
                document.getElementById("cust_email").value = data.Cust_Email || '';
                document.getElementById("cust_city").value = data.Cust_City || '';
                document.getElementById("cust_gender").value = data.Cust_Gender || '';
            }
        })
        .catch(err => console.error("Fetch error:", err));
}

function openEditModal(id, nextDate, reason) {
    document.getElementById("edit_followup_id").value = id;
    document.getElementById("edit_next_followup_date").value = nextDate;
    document.getElementById("edit_reason").value = reason;
    document.getElementById("editModal").style.display = "flex";
}
function closeModal(){ document.getElementById("editModal").style.display="none"; }
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
            <li><a href="followups.php"><span class="icon"><i class="fas fa-phone-volume"></i></span><span class="title">Follow-ups</span></a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <h1>Follow-up Management</h1>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <!-- Add Form -->
        <form method="post">
            <fieldset>
                <legend>Add Follow-up</legend>

                <label>Follow-up Date:</label>
                <input type="date" name="followup_date" required>

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

                <label>City:</label>
                <input type="text" id="cust_city" readonly>

                <label>Gender:</label>
                <input type="text" id="cust_gender" readonly>

                <label>Next Follow-up Date:</label>
                <input type="date" name="next_followup_date" required>

                <label>Follow-up Reason:</label>
                <textarea name="reason" rows="3" placeholder="Enter reason or notes..." required></textarea>

                <button type="submit" name="add_followup"><i class="fas fa-save"></i> Save Follow-up</button>
            </fieldset>
        </form>

        <!-- List of Follow-ups -->
        <h2>Follow-up History</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Follow-up Date</th>
                    <th>Customer Name</th>
                    <th>Number</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Next Follow-up</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($followups as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['Followup_Id']) ?></td>
                        <td><?= htmlspecialchars($f['Followup_Date']) ?></td>
                        <td><?= htmlspecialchars($f['Cust_Name']) ?></td>
                        <td><?= htmlspecialchars($f['Cust_Number']) ?></td>
                        <td><?= htmlspecialchars($f['Cust_Email']) ?></td>
                        <td><?= htmlspecialchars($f['Cust_City']) ?></td>
                        <td><?= htmlspecialchars($f['Next_Followup_Date']) ?></td>
                        <td><?= htmlspecialchars($f['Followup_Reason']) ?></td>
                        <td class="action-btns">
                            <a href="javascript:void(0);" 
                               class="edit-btn"
                               onclick="openEditModal('<?= $f['Followup_Id'] ?>','<?= $f['Next_Followup_Date'] ?>','<?= htmlspecialchars(addslashes($f['Followup_Reason'])) ?>')">
                               <i class="fas fa-edit"></i>
                            </a>
                            <a href="followups.php?delete=<?= $f['Followup_Id'] ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Delete this follow-up?')">
                               <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Edit Follow-up</h2>
        <form method="post">
            <input type="hidden" name="followup_id" id="edit_followup_id">

            <label>Next Follow-up Date:</label>
            <input type="date" name="edit_next_followup_date" id="edit_next_followup_date" required>

            <label>Follow-up Reason:</label>
            <textarea name="edit_reason" id="edit_reason" rows="3" required></textarea>

            <button type="submit" name="update_followup"><i class="fas fa-sync-alt"></i> Update</button>
        </form>
    </div>
</div>
</body>
</html>
