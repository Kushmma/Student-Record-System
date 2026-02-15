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

// Fetch student results (recent 3)
$sql_results = "SELECT subject, marks, grade, created_at FROM results WHERE student_id = ? ORDER BY created_at DESC LIMIT 3";
$stmt2 = $conn->prepare($sql_results);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$results = $stmt2->get_result();

// Fetch finance details
$sql_fees = "SELECT * FROM finances WHERE student_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt3 = $conn->prepare($sql_fees);
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$fees = $stmt3->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> | Dashboard</title>
    <link rel="stylesheet" href="../css/student_dashboard.css">
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
            <li><a href="student_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="results.php"><i class="fas fa-chart-line"></i> Results</a></li>
            <li><a href="fees.php"><i class="fas fa-coins"></i> Fees</a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>

<!-- 💻 Main Content -->
<main class="main-content">
    <header class="welcome-header">
        <div class="welcome-left">
            <img src="../images/default-avatar.png" alt="Avatar">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?> 👋</h1>
                <p>Semester: <?php echo htmlspecialchars($user['semester']); ?> | Class: <?php echo htmlspecialchars($user['class']); ?></p>
            </div>
        </div>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </header>

    <!-- 🧩 Dashboard Cards -->
    <section class="dashboard-cards">
        <div class="card profile-card">
            <i class="fas fa-user-graduate icon"></i>
            <h3>Profile Info</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>

        <div class="card finance-card">
            <i class="fas fa-coins icon"></i>
            <h3>Finance Summary</h3>
            <?php if ($fees): ?>
                <p><strong>Amount Due:</strong> ₨ <?php echo htmlspecialchars($fees['amount_due']); ?></p>
                <p><strong>Paid:</strong> ₨ <?php echo htmlspecialchars($fees['amount_paid']); ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge <?php echo strtolower($fees['status']); ?>">
                        <?php echo htmlspecialchars($fees['status']); ?>
                    </span>
                </p>
            <?php else: ?>
                <p>No finance records available.</p>
            <?php endif; ?>
        </div>

        <div class="card dates-card">
            <i class="fas fa-calendar-alt icon"></i>
            <h3>Important Dates</h3>
            <ul>
                <li>🧾 Midterm Exam: <b>Nov 10, 2025</b></li>
                <li>🎓 Project Deadline: <b>Dec 1, 2025</b></li>
                <li>💰 Fee Due: <b>Nov 25, 2025</b></li>
            </ul>
        </div>
    </section>

    <!-- 📊 Latest Results -->
    <section class="results-section">
        <div class="results-card">
            <h2>📘 Recent Results</h2>
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
                <p class="no-results">No results yet.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

</body>
</html>