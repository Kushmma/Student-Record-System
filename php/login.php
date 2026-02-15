<?php
session_start();
include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = mysqli_real_escape_string($conn, $_POST['username_email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE username='$username_email' OR email='$username_email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // If passwords are hashed
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            if ($user['is_admin'] == 1) {
                header("Location: admin_dashboard.php");
                exit;
            } else {
                header("Location: student_dashboard.php");
                exit;
            }
        } else {
            $message = "⚠️ Incorrect password!";
        }
    } else {
        $message = "⚠️ User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login | SRMS</title>
<link rel="stylesheet" href="../css/sty.css">
</head>
<body>
<div class="form-container">
<h2>Login</h2>
<form method="POST">
<input type="text" name="username_email" placeholder="Username or Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button type="submit">Login</button>
</form>
<p>Not registered? <a href="register.php">Sign Up</a></p>
<p class="message"><?php echo htmlspecialchars($message); ?></p>
</div>
</body>
</html>
