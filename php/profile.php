<?php
session_start();
include 'db.php';

// Only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] == 1) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> | Profile</title>
    <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- 🌐 Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../images/third.png" alt="Logo" class="logo">
        <h1>Smartech</h1>
        <h2>Student Panel</h2>
    </div>
    <nav class="nav-links">
        <ul>
            <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="results.php"><i class="fas fa-chart-line"></i> Results</a></li>
            <li><a href="fees.php"><i class="fas fa-coins"></i> Fees</a></li>
            <li><a href="login.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>

<!-- 💻 Main Content -->
<main class="main-content">
    <header class="welcome-header">
        <div class="welcome-left">
            <img src="../images/default-avatar.png" alt="Avatar">
            <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> 👋</h1>
        </div>
        <a href="login.php" class="logout-btn">🚪 Logout</a>
    </header>

    <section class="profile-section">
        <div class="profile-card">
            <div class="profile-header">
                <img src="images/default-avatar.png" alt="User Avatar" class="avatar">
                <h2><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h2>
                <p>@<?php echo htmlspecialchars($user['username']); ?></p>
                <p class="status"><?php echo htmlspecialchars($user['status'] ?? 'Active'); ?></p>
            </div>

            <div class="profile-details">
                <h3>Student Information</h3>
                <div class="info-grid">
                    <?php
                    $ignore = ['id','password','is_admin'];
                    foreach($user as $key=>$value){
                        if(in_array($key,$ignore)) continue;
                        if($value === null || $value === '') $value = '<em>Not provided</em>';
                        $label = ucwords(str_replace("_"," ",$key));
                        echo "<div><strong>$label:</strong> ".htmlspecialchars($value)."</div>";
                    }
                    ?>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit_profile.php" class="btn edit-btn">✏️ Edit Profile</a>
            </div>
        </div>
    </section>
</main>

</body>
</html>
