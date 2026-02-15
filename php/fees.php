<?php
session_start();
include 'db.php';

// ✅ Access check
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] == 1) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch student info
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ✅ Fetch finance records
$sql_fees = "SELECT * FROM finances WHERE student_id = ?";
$stmt2 = $conn->prepare($sql_fees);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$fees_result = $stmt2->get_result();
$fees = $fees_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finance | <?php echo htmlspecialchars($user['first_name']); ?></title>
    <link rel="stylesheet" href="../css/fees.css">
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
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="results.php"><i class="fas fa-chart-line"></i> Results</a></li>
            <li><a href="fees.php" class="active"><i class="fas fa-coins"></i> Fees</a></li>
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

    <section class="fees-section">
        <div class="fees-card">
            <h2>💰 Your Fee Details</h2>
            <p class="subtitle">View your payment records and status</p>

            <?php if (count($fees) > 0): ?>
            <div class="table-wrapper">
                <table class="fees-table">
                    <thead>
                        <tr>
                            <th>Amount Due (Rs.)</th>
                            <th>Amount Paid (Rs.)</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $row): ?>
                        <tr>
                            <td><?php echo number_format($row['amount_due'], 2); ?></td>
                            <td><?php echo number_format($row['amount_paid'], 2); ?></td>
                            <td><?php echo $row['payment_date'] ? htmlspecialchars($row['payment_date']) : '—'; ?></td>
                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['remarks'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="no-data">No fee records found for your account.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>
