<?php
session_start();
include 'db.php';

// ✅ Only Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = "";

// ---------------- ADD or UPDATE RESULT ----------------
if (isset($_POST['save_result'])) {
    $id = intval($_POST['id']);
    $student_id = intval($_POST['student_id']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $marks = floatval($_POST['marks']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);

    if ($id > 0) {
        // Update
        $query = "UPDATE results SET student_id='$student_id', subject='$subject', marks='$marks', grade='$grade' WHERE id='$id'";
        if (mysqli_query($conn, $query)) {
            $message = "✅ Result updated successfully!";
        } else {
            $message = "❌ Failed to update result: " . mysqli_error($conn);
        }
    } else {
        // Add New
        $query = "INSERT INTO results (student_id, subject, marks, grade, created_at) 
                  VALUES ('$student_id', '$subject', '$marks', '$grade', NOW())";
        if (mysqli_query($conn, $query)) {
            $message = "✅ Result added successfully!";
        } else {
            $message = "❌ Failed to add result: " . mysqli_error($conn);
        }
    }
}

// ---------------- DELETE RESULT ----------------
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM results WHERE id='$delete_id'");
    $message = "🗑️ Result deleted successfully!";
}

// ---------------- PAGINATION + SEARCH ----------------
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Get total
$total_query = "SELECT COUNT(*) AS total FROM results r 
                LEFT JOIN students s ON r.student_id = s.id
                WHERE s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' OR r.subject LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_query);
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch results with student info
$query = "SELECT r.*, CONCAT(s.first_name,' ',s.last_name) AS student_name
          FROM results r
          LEFT JOIN students s ON r.student_id = s.id
          WHERE s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' OR r.subject LIKE '%$search%'
          ORDER BY r.id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Results | Smartech</title>
<link rel="stylesheet" href="../css/manage_result.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../images/third.png" alt="Logo" class="logo">
        <h1>Smartech</h1>
    </div>
    <nav class="nav-links">
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
            <li><a href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <li><a href="manage_results.php" class="active"><i class="fas fa-file-alt"></i> Manage Results</a></li>
            <li><a href="manage_finances.php">💰 Manage Finance</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <div class="manage-container">
        <div class="header-section">
            <h2><i class="fas fa-file-alt"></i> Manage Results</h2>
            <?php if ($message) echo "<div class='message'>$message</div>"; ?>
        </div>

        <!-- Search + Add -->
        <div class="action-bar">
            <form method="GET" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by student or subject..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="search-btn">Search</button>
            </form>
            <button class="add-btn" onclick="openModal()">+ Add Result</button>
        </div>

        <!-- Results Table -->
        <div class="table-container">
            <table class="results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars($row['marks']) ?></td>
                            <td><?= htmlspecialchars($row['grade']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td>
                                <button onclick='openModal(<?= json_encode($row) ?>)' class="btn-edit"><i class="fas fa-edit"></i></button>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure to delete this result?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-data">No results found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Add/Edit Modal -->
<div id="resultModal" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-pen"></i> Manage Result</h3>
        <form method="POST">
            <input type="hidden" name="id" id="result_id">
            <div class="form-group">
                <label>Student ID:</label>
                <input type="number" name="student_id" id="student_id" required>
            </div>
            <div class="form-group">
                <label>Subject:</label>
                <input type="text" name="subject" id="subject" required>
            </div>
            <div class="form-group">
                <label>Marks:</label>
                <input type="number" name="marks" id="marks" required>
            </div>
            <div class="form-group">
                <label>Grade:</label>
                <input type="text" name="grade" id="grade" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" name="save_result" class="btn-submit">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(data = null) {
    if (data) {
        document.getElementById('result_id').value = data.id;
        document.getElementById('student_id').value = data.student_id;
        document.getElementById('subject').value = data.subject;
        document.getElementById('marks').value = data.marks;
        document.getElementById('grade').value = data.grade;
    } else {
        document.getElementById('result_id').value = '';
        document.getElementById('student_id').value = '';
        document.getElementById('subject').value = '';
        document.getElementById('marks').value = '';
        document.getElementById('grade').value = '';
    }
    document.getElementById('resultModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('resultModal').style.display = 'none';
}
window.onclick = function(e) {
    if (e.target == document.getElementById('resultModal')) closeModal();
};
</script>

</body>
</html>
