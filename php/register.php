<?php
include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Optional additional fields
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');

    // Password confirmation
    if ($password !== $confirm) {
        $message = "⚠️ Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Check if email or username already exists
        $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email' OR username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $message = "⚠️ Email or Username already registered!";
        } else {
            $sql = "INSERT INTO students (first_name, last_name, username, email, password, phone, gender) 
                    VALUES ('$first_name', '$last_name', '$username', '$email', '$hashed', '$phone', '$gender')";
            if (mysqli_query($conn, $sql)) {
                $message = "✅ Registration successful! <a href='login.php'>Login now</a>";
            } else {
                $message = "❌ Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | SRMS</title>
    <link rel="stylesheet" href="../css/sty.css">
    <script>
    function validatePasswords() {
        const pass = document.getElementById("password").value;
        const confirm = document.getElementById("confirm_password").value;
        if (pass !== confirm) {
            alert("Passwords do not match!");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
<div class="form-container">
    <h2>Register</h2>
    <form method="POST" onsubmit="return validatePasswords()">
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required><br>
        <input type="text" name="phone" placeholder="Phone Number"><br>
        <select name="gender">
            <option value="" disabled selected>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>
        <button type="submit">Sign Up</button>
    </form>
    <p>Already registered? <a href="login.php">Login</a></p>
    <p class="message"><?php echo $message; ?></p>
</div>
</body>
</html>
