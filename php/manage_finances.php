<?php
session_start();
include 'db.php';

// ✅ Only Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// 🧾 Handle Add or Update Finance Record
if (isset($_POST['save_finance'])) {
    $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : 0;
    $student_id = intval($_POST['student_id']);
    $amount_due = floatval($_POST['amount_due']);
    $amount_paid = floatval($_POST['amount_paid']);
    $payment_date = $_POST['payment_date'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    // Auto-update status
    if ($amount_paid >= $amount_due) $status = 'Paid';
    else if ($amount_paid > 0 && $amount_paid < $amount_due) $status = 'Pending';
    else $status = 'Overdue';

    if ($id > 0) {
        // Update existing
        $query = "UPDATE finances 
                  SET student_id='$student_id', amount_due='$amount_due', amount_paid='$amount_paid', 
                      payment_date='$payment_date', status='$status', remarks='$remarks' 
                  WHERE id='$id'";
        mysqli_query($conn, $query);
        $message = "✅ Finance record updated successfully!";
    } else {
        // Insert new
        $query = "INSERT INTO finances (student_id, amount_due, amount_paid, payment_date, status, remarks) 
                  VALUES ('$student_id', '$amount_due', '$amount_paid', '$payment_date', '$status', '$remarks')";
        mysqli_query($conn, $query);
        $message = "✅ Finance record added successfully!";
    }
}

// 🗑 Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM finances WHERE id='$delete_id'");
    $message = "🗑 Finance record deleted successfully!";
}

// 🔍 Search and Pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$total_query = "SELECT COUNT(*) AS total FROM finances f 
                JOIN students s ON f.student_id = s.id
                WHERE s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' OR s.username LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $limit);

// Fetch records
$query = "SELECT f.*, s.first_name, s.last_name, s.username 
          FROM finances f 
          JOIN students s ON f.student_id = s.id
          WHERE s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' OR s.username LIKE '%$search%'
          ORDER BY f.id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Finances | Smartech College</title>
<link rel="stylesheet" href="../css/manage_finances.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../images/third.png" class="logo">
        <h1>Smartech </h1>
    </div>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
            <li><a href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <li><a href="manage_results.php"><i class="fas fa-file"></i> Manage Results</a></li>
            <li><a href="manage_finances.php" class="active"><i class="fas fa-dollar-sign"></i> Manage Finances</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>

<main class="main-content">
    <div class="container">
        <h2><i class="fas fa-wallet"></i> Manage Finances</h2>
        <?php if(isset($message)): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>

        <div class="action-bar">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by student name or username..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
            <button class="add-btn" onclick="openModal()"><i class="fas fa-plus"></i> Add Record</button>
        </div>

        <table class="finance-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?> (<?= $row['username'] ?>)</td>
                        <td>Rs. <?= number_format($row['amount_due'], 2) ?></td>
                        <td>Rs. <?= number_format($row['amount_paid'], 2) ?></td>
                        <td>
                            <span class="badge <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span>
                        </td>
                        <td><?= $row['payment_date'] ?: 'N/A' ?></td>
                        <td><?= htmlspecialchars($row['remarks'] ?: '-') ?></td>
                        <td>
                            <button class="edit-btn" onclick='openModal(<?= json_encode($row) ?>)'><i class="fas fa-edit"></i></button>
                            <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="no-data">No finance records found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php for($i=1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="financeModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Finance Record</h3>
            <form method="POST" class="finance-form">
                <input type="hidden" name="id" id="finance_id">
                <label>Student ID:</label>
                <input type="number" name="student_id" id="student_id" required>

                <label>Amount Due (Rs):</label>
                <input type="number" step="0.01" name="amount_due" id="amount_due" required>

                <label>Amount Paid (Rs):</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" required>

                <label>Payment Date:</label>
                <input type="date" name="payment_date" id="payment_date">

                <label>Remarks:</label>
                <input type="text" name="remarks" id="remarks" placeholder="Optional">

                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="cancel-btn">Cancel</button>
                    <button type="submit" name="save_finance" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function openModal(data = null) {
    if (data) {
        document.getElementById('finance_id').value = data.id;
        document.getElementById('student_id').value = data.student_id;
        document.getElementById('amount_due').value = data.amount_due;
        document.getElementById('amount_paid').value = data.amount_paid;
        document.getElementById('payment_date').value = data.payment_date;
        document.getElementById('remarks').value = data.remarks;
    } else {
        document.querySelector('.finance-form').reset();
        document.getElementById('finance_id').value = '';
    }
    document.getElementById('financeModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('financeModal').style.display = 'none';
}
window.onclick = function(e) {
    if (e.target == document.getElementById('financeModal')) closeModal();
}
</script>
</body>
</html>
