<?php
session_start();
include 'db.php'; // Database connection

// Access only for admins
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = "";
$message_class = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $gender     = $_POST['gender'];
    $password   = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match!";
        $message_class = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database safely
        $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, username, email, password, phone, gender, is_admin, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("sssssss", $first_name, $last_name, $username, $email, $hashed_password, $phone, $gender);

        if ($stmt->execute()) {
            $message = "✅ Student added successfully!";
            $message_class = "success";
        } else {
            $message = "❌ Error: " . $stmt->error;
            $message_class = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student | Smartech Admin</title>
<link rel="stylesheet" href="../css/add_student.css">
</head>
<body>

<!-- Sidebar -->
    <div class="sidebar">
    <img src="../images/third.png" alt="Logo">
    <h1>Smartech</h1>
    <ul>
        <li><a href="admin_dashboard.php">🏠 Dashboard</a></li>
        <li><a href="add_student.php" class="active">➕ Add Student</a></li>
        <li><a href="manage_students.php">👨‍🎓 Manage Students</a></li>
        <li><a href="manage_results.php">📊Upload Result</a></li>
        <li><a href="manage_finances.php">💰Manage Finance</a></li>
        <li><a href="login.php">🚪 Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="form-container">
        <h1>➕ Add New Student</h1>
        <p class="subtitle">Enter student details below to register them into the system.</p>

        <form method="POST" class="add-student-form">
            <div class="row">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="row">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="row">
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="row">
                <input type="text" name="phone" placeholder="Phone Number" required>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <button type="submit" class="btn">Add Student</button>
        </form>

        <?php if ($message): ?>
            <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
