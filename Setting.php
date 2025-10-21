<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("config.php"); // PDO connection

// Fetch current settings (if missing, insert defaults)
$stmt = $pdo->query("SELECT * FROM settings WHERE id=1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$settings) {
    $pdo->exec("INSERT INTO settings (id, theme, notify_email, gst_percentage, profile_pic)
                VALUES (1, 'neon', 0, 18, '')");
    $settings = [
        'theme' => 'neon',
        'notify_email' => 0,
        'gst_percentage' => 18,
        'profile_pic' => ''
    ];
}

$success = $error = '';

if (isset($_POST['save'])) {
    $theme = $_POST['theme'] ?? 'neon';
    $notify_email = isset($_POST['notify_email']) ? 1 : 0;
    $gst_percentage = $_POST['gst_percentage'] ?? 18;

// Profile Picture Upload Section
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
        if ($_FILES['profile_pic']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = 'profile_' . time() . '.' . $ext;
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadDir . $filename)) {
                    $update = $pdo->prepare("UPDATE settings SET profile_pic=? WHERE id=1");
                    $update->execute([$filename]);
                    $settings['profile_pic'] = $filename;
                    $success = "✅ Profile picture updated successfully!";
                } else {
                    $error = "❌ Failed to upload profile picture.";
                }
            } else {
                $error = "❌ Only JPG, PNG, GIF, WEBP allowed.";
            }
        }
    }

    // Update database
    $update = $pdo->prepare("UPDATE settings SET theme=?, notify_email=?, gst_percentage=?, profile_pic=? WHERE id=1");
    if ($update->execute([$theme, $notify_email, $gst_percentage, $profile_pic])) {
        $success = "✅ Settings updated successfully!";
        $stmt = $pdo->query("SELECT * FROM settings WHERE id=1");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error .= "❌ Failed to update settings.<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CRM Settings</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/82a283d995.js" crossorigin="anonymous"></script>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #0f0f2f;
    color: #00ffff;
    margin: 0;
    padding: 20px;
    transition: background 0.5s, color 0.5s;
    font-size: 16px;
}
h1 {
    color: #00ffff;
    text-align: center;
    margin-bottom: 20px;
    font-size: 28px;
}

form {
    max-width: 600px;
    margin: auto;
    background: #1a1a3f;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0,255,255,0.3);
}
fieldset {
    border: 1px solid #00ffff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}
legend {
    padding: 0 10px;
    font-weight: bold;
    color: #00ffff;
    font-size: 18px;
}
label {
    display: block;
    margin: 10px 0 5px;
    font-weight: 500;
    font-size: 15px;
}
input[type="text"], input[type="number"], select, input[type="file"] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #00ffff;
    background: #0f0f2f;
    color: #fff;
    font-size: 16px;
    margin-bottom: 10px;
}
button {
    background: #00ffff;
    color: #111126;
    border: none;
    padding: 12px 30px;
    font-size: 17px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    width: 100%;
}
button:hover { background: #00bfbf; }

.success, .error {
    text-align: center;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 16px;
}
.success { color: #00ff7f; }
.error { color: #ff4d4d; }

.profile-pic {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #00ffff;
    background: #000;
}


.btn-dashboard {
    display: inline-block;
    padding: 10px 25px;
    background: #00ffff;
    color: #111126;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 500;
    transition: 0.3s;
    margin-bottom: 20px;
}
.btn-dashboard:hover { background: #00bfbf; }
.btn-dashboard i { margin-right: 8px; }

/* THEME VARIANTS */
body.light { background: #f5f5f5; color: #111; }
body.light form { background: #fff; box-shadow: 0 0 25px rgba(0,0,0,0.1); }
body.dark { background: #1e1e2f; color: #fff; }
body.dark form { background: #2e2e50; box-shadow: 0 0 25px rgba(0,255,255,0.1); }
body.neon { background: #0f0f2f; color: #00ffff; }
body.neon form { background: #1a1a3f; box-shadow: 0 0 25px #0ff; }
</style>
</head>
<body class="<?= htmlspecialchars($settings['theme'] ?? 'neon') ?>">

<h1>CRM Settings</h1>

<div style="text-align:center;">
    <a href="index.html" class="btn-dashboard"><i class="fas fa-home"></i> Back to Dashboard</a>
</div>

<?php if($success) echo "<p class='success'>$success</p>"; ?>
<?php if($error) echo "<p class='error'>$error</p>"; ?>

<form method="post" enctype="multipart/form-data" id="settingsForm">
        <fieldset>
        <legend>Profile Picture</legend>
        <div style="text-align:center;">
            <img 
                id="profilePreview"
                src="<?= (!empty($settings['profile_pic']) && file_exists('uploads/'.$settings['profile_pic'])) 
                    ? 'uploads/'.$settings['profile_pic'] 
                    : 'https://via.placeholder.com/120x120?text=Profile'; ?>" 
                class="profile-pic" 
                alt="Profile Picture">
        </div>
        <input type="file" name="profile_pic" id="profileInput" accept="image/*" style="margin-top:10px;">
        <button type="submit" name="save" style="margin-top:10px;"><i class="fas fa-upload"></i> Upload</button>
    </fieldset>

        <legend>Theme</legend>
        <select name="theme" id="themeSelect">
            <option value="light" <?= $settings['theme']=='light'?'selected':'' ?>>Light</option>
            <option value="dark" <?= $settings['theme']=='dark'?'selected':'' ?>>Dark</option>
            <option value="neon" <?= $settings['theme']=='neon'?'selected':'' ?>>Neon</option>
        </select>
    </fieldset>

    <fieldset>
        <legend>Notifications</legend>
        <label>
            <input type="checkbox" name="notify_email" <?= $settings['notify_email']?'checked':'' ?>> Email Notifications
        </label>
    </fieldset>

    <fieldset>
        <legend>GST / Tax Settings</legend>
        <label>GST Percentage:</label>
        <input type="number" name="gst_percentage" value="<?= htmlspecialchars($settings['gst_percentage']) ?>" min="0" max="100" required>
    </fieldset>

    <button type="submit" name="save"><i class="fas fa-save"></i> Save Settings</button>
</form>

<script src="js/settings.js"></script>
</body>
</html>
