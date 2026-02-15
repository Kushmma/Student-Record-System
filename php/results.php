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

// Fetch student results
$sql_results = "SELECT subject, marks, grade, created_at FROM results WHERE student_id = ?";
$stmt2 = $conn->prepare($sql_results);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$results = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> | Results</title>
    <link rel="stylesheet" href="../css/results.css">
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
            <li><a href="results.php" class="active"><i class="fas fa-chart-line"></i> Results</a></li>
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

    <section class="results-section">
        <div class="results-card">
            <h2>📘 Academic Results</h2>

            <?php if ($results->num_rows > 0): ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo htmlspecialchars($row['marks']); ?></td>
                        <td><?php echo htmlspecialchars($row['grade'] ?? 'Pending'); ?></td>
                        <td><?php echo htmlspecialchars(date("M d, Y", strtotime($row['created_at']))); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="no-results">No results available yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>
