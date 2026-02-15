<?php
session_start();
include 'db.php';

// ✅ Only Admins can access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ===============================
// 🔹 FETCH LIVE DATA FROM DATABASE
// ===============================

// Total Students
$students_query = "SELECT COUNT(*) AS total_students FROM students WHERE is_admin = 0";
$students_result = mysqli_query($conn, $students_query);
$total_students = mysqli_fetch_assoc($students_result)['total_students'] ?? 0;

// Total Teachers (if you have a teachers table)
$teachers_query = "SELECT COUNT(*) AS total_teachers FROM teachers";
$teachers_result = mysqli_query($conn, $teachers_query);
$total_teachers = mysqli_fetch_assoc($teachers_result)['total_teachers'] ?? 0;

// Total Finance (Total Paid Amount)
$income_query = "SELECT COALESCE(SUM(amount_paid), 0) AS total_income FROM finances";
$income_result = mysqli_query($conn, $income_query);
$total_income = mysqli_fetch_assoc($income_result)['total_income'] ?? 0;

// Total Results Uploaded
$results_query = "SELECT COUNT(*) AS total_results FROM results";
$results_result = mysqli_query($conn, $results_query);
$total_results = mysqli_fetch_assoc($results_result)['total_results'] ?? 0;

// Fetch recent finance transactions
$recent_payments = mysqli_query($conn, "
    SELECT f.*, s.first_name, s.last_name 
    FROM finances f
    JOIN students s ON f.student_id = s.id 
    ORDER BY f.payment_date DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Smartech College</title>
<link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../images/third.png" alt="Smartech College logo featuring graduation cap and books" class="logo">
        <h1>Smartech</h1>
    </div>

    <nav class="nav-links">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="admin_dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="add_student.php">➕ Add Student</a></li>
            <li><a href="manage_students.php">👨‍🎓 Manage Students</a></li>
            <li><a href="manage_results.php">📊 Upload Result</a></li>
            <li><a href="manage_finances.php">💰 Manage Finance</a></li>
            <li><a href="login.php">🚪 Logout</a></li>
        </ul>
    </nav>
</aside>

<!-- Main Section -->
<main class="main-content">
    <header class="welcome-header">
        <div class="welcome-left">
            <img src="../images/third.png" alt="Admin profile icon representing system administrator">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
        </div>
        <div class="welcome-right">
            <button onclick="location.href='logout.php'" class="logout-btn">Logout</button>
        </div>
    </header>

    <!-- Dashboard Summary Cards -->
    <section class="dashboard-cards">
        <div class="card">
            <h3>👨‍🎓 Total Students</h3>
            <p><?php echo $total_students; ?></p>
        </div>
        <div class="card">
            <h3>👩‍🏫 Total Teachers</h3>
            <p><?php echo $total_teachers; ?></p>
        </div>
        <div class="card">
            <h3>💰 Total Income</h3>
            <p>₨ <?php echo number_format($total_income, 2); ?></p>
        </div>
        <div class="card">
            <h3>📑 Results Uploaded</h3>
            <p><?php echo $total_results; ?></p>
        </div>
    </section>

    <!-- New Panels Section -->
    <section class="extra-panels">
        <!-- Upcoming Events -->
        <div class="panel">
            <h2>📅 Upcoming Academic Events</h2>
            <ul class="event-list">
                <li><strong>Mid-Term Exams:</strong> November 10–15, 2025</li>
                <li><strong>Teacher Meeting:</strong> October 25, 2025</li>
                <li><strong>Sports Week:</strong> December 1–5, 2025</li>
                <li><strong>Annual Day:</strong> December 20, 2025</li>
            </ul>
        </div>

        <!-- Latest Finance -->
        <div class="panel">
            <h2>🧾 Latest Fee Payments</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Amount Paid (₨)</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($recent_payments) > 0) {
                        while ($row = mysqli_fetch_assoc($recent_payments)) {
                            echo "<tr>
                                    <td>{$row['first_name']} {$row['last_name']}</td>
                                    <td>{$row['amount_paid']}</td>
                                    <td>{$row['status']}</td>
                                    <td>{$row['payment_date']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No recent payments.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Announcements -->
        <div class="panel">
            <h2>📢 Announcements</h2>
            <ul class="announcement-list">
                <li>⚙️ Server maintenance scheduled on October 28, 2025.</li>
                <li>🎓 Final year projects submission deadline: November 30, 2025.</li>
                <li>🧾 Fee due reminder emails will be sent this week.</li>
                <li>📋 New teacher recruitment drive starting soon.</li>
            </ul>
        </div>
    </section>
</main>
</body>
</html>
