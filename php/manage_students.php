<?php
session_start();
include 'db.php';

// ✅ Only Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$message = "";

// ✅ Handle Update Submission
if(isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    
    // Debug: Check if we're receiving the POST data
    error_log("Update attempt for student ID: " . $id);
    
    // Define allowed fields - Expanded with new features
    $fields = [
        'first_name', 'last_name', 'username', 'email',
        'phone', 'gender', 'semester', 'class', 'roll_no', 'status',
        'date_of_birth', 'address', 'city', 'state', 'zip_code', 
        'country', 'parent_name', 'parent_phone', 'emergency_contact',
        'blood_group', 'admission_date', 'course', 'department'
    ];

    // Build update query securely
    $updates = [];
    foreach($fields as $field) {
        if(isset($_POST[$field])) {
            $value = mysqli_real_escape_string($conn, trim($_POST[$field]));
            $updates[] = "`$field` = '$value'";
        }
    }

    // Handle password update if provided
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updates[] = "`password` = '$password'";
    }

    if(!empty($updates)) {
        $update_query = "UPDATE students SET " . implode(', ', $updates) . " WHERE id = '$id'";
        
        error_log("SQL Query: " . $update_query); // Debug logging
        
        if(mysqli_query($conn, $update_query)) {
            if(mysqli_affected_rows($conn) > 0) {
                $message = "✅ Student record updated successfully!";
                // Redirect to avoid form resubmission
                header("Location: manage_students.php?success=1&search=" . urlencode($_GET['search'] ?? '') . "&page=" . ($_GET['page'] ?? 1));
                exit;
            } else {
                $message = "⚠️ No changes made or student not found.";
            }
        } else {
            $message = "❌ Update failed: " . mysqli_error($conn);
            error_log("MySQL Error: " . mysqli_error($conn));
        }
    } else {
        $message = "⚠️ No fields to update.";
    }
}

// ✅ Handle Bulk Actions
if(isset($_POST['bulk_action'])) {
    if(isset($_POST['selected_students']) && !empty($_POST['selected_students'])) {
        $selected_ids = implode(',', array_map('intval', $_POST['selected_students']));
        
        switch($_POST['bulk_action']) {
            case 'activate':
                $bulk_query = "UPDATE students SET status='Active' WHERE id IN ($selected_ids)";
                $action_message = "activated";
                break;
            case 'deactivate':
                $bulk_query = "UPDATE students SET status='Inactive' WHERE id IN ($selected_ids)";
                $action_message = "deactivated";
                break;
            case 'delete':
                $bulk_query = "DELETE FROM students WHERE id IN ($selected_ids) AND is_admin=0";
                $action_message = "deleted";
                break;
            default:
                $bulk_query = "";
        }
        
        if(!empty($bulk_query) && mysqli_query($conn, $bulk_query)) {
            $affected_rows = mysqli_affected_rows($conn);
            $message = "✅ $affected_rows students $action_message successfully!";
        }
    } else {
        $message = "⚠️ Please select students to perform bulk action.";
    }
}

// Show success message if redirected
if(isset($_GET['success'])) {
    $message = "✅ Student record updated successfully!";
}

// ✅ Handle Delete Action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM students WHERE id='$delete_id' AND is_admin=0";
    if(mysqli_query($conn, $delete_query)) {
        $message = "🗑️ Student record deleted successfully!";
        header("Location: manage_students.php?success_delete=1&search=" . urlencode($_GET['search'] ?? '') . "&page=" . ($_GET['page'] ?? 1));
        exit;
    } else {
        $message = "❌ Delete failed: " . mysqli_error($conn);
    }
}

// Show delete success message
if(isset($_GET['success_delete'])) {
    $message = "🗑️ Student record deleted successfully!";
}

// ✅ Handle Export
if(isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=students_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, [
        'ID', 'First Name', 'Last Name', 'Username', 'Email', 'Phone', 
        'Gender', 'Semester', 'Class', 'Roll No', 'Status', 'Date of Birth',
        'Address', 'City', 'Parent Name', 'Parent Phone', 'Emergency Contact', 'Admission Date',
        'Course', 'Department', 'Created At'
    ]);
    
    $export_query = "SELECT * FROM students WHERE is_admin=0 ORDER BY id DESC";
    $export_result = mysqli_query($conn, $export_query);
    
    while($student = mysqli_fetch_assoc($export_result)) {
        fputcsv($output, $student);
    }
    
    fclose($output);
    exit;
}

// Pagination and Search
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Build WHERE clause for filters
$where_conditions = ["is_admin=0"];
if(!empty($search)) {
    $where_conditions[] = "(first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%' OR roll_no LIKE '%$search%')";
}
if(!empty($status_filter)) {
    $where_conditions[] = "status='$status_filter'";
}
$where_clause = implode(' AND ', $where_conditions);

// Total students for pagination
$total_query = "SELECT COUNT(*) AS total FROM students WHERE $where_clause";
$total_result = mysqli_query($conn, $total_query);
$total_students = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_students / $limit);

// Fetch students with filters
$students_query = "SELECT * FROM students WHERE $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
$students_result = mysqli_query($conn, $students_query);

if(!$students_result) {
    $message = "❌ Database error: " . mysqli_error($conn);
}

// Get statistics for dashboard
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status='Inactive' THEN 1 ELSE 0 END) as inactive,
    COUNT(DISTINCT class) as classes,
    COUNT(DISTINCT semester) as semesters
    FROM students WHERE is_admin=0";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Students | Smartech College</title>
<link rel="stylesheet" href="../css/manage_student.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../images/third.png" alt="Logo" class="logo">
        <h1>Smartech</h1>
    </div>
    <nav class="nav-links">
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="add_student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
            <li><a href="manage_students.php" class="active"><i class="fas fa-users-cog"></i> Manage Students</a></li>
            <li><a href="manage_results.php"><i class="fas fa-file-upload"></i> Upload Results</a></li>
            <li><a href="manage_finances.php"><i class="fas fa-chart-bar"></i> Manage finances</a></li>
            <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>

<!-- Main Content -->
<main class="main-content">
<div class="manage-container">
    <div class="header-section">
        <h2><i class="fas fa-users-cog"></i> Manage Students</h2>
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['total'] ?></span>
                    <span class="stat-label">Total Students</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['active'] ?></span>
                    <span class="stat-label">Active</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon inactive">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['inactive'] ?></span>
                    <span class="stat-label">Inactive</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon classes">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value"><?= $stats['classes'] ?></span>
                    <span class="stat-label">Classes</span>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($message)): ?>
        <div class='message <?= strpos($message, '✅') !== false || strpos($message, '🗑️') !== false ? 'success' : 'error' ?>'>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Action Bar -->
    <div class="action-bar">
        <form method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, username, email or roll no..." value="<?= htmlspecialchars($search); ?>">
            </div>
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <option value="Active" <?= $status_filter=='Active'?'selected':'' ?>>Active</option>
                <option value="Inactive" <?= $status_filter=='Inactive'?'selected':'' ?>>Inactive</option>
            </select>
            <button type="submit" class="search-btn">Filter</button>
            <?php if(!empty($search) || !empty($status_filter)): ?>
                <a href="manage_students.php" class="clear-btn">Clear</a>
            <?php endif; ?>
        </form>
        <div class="action-buttons">
            <a href="?export=1" class="export-btn"><i class="fas fa-download"></i> Export CSV</a>
            <a href="add_student.php" class="add-btn"><i class="fas fa-user-plus"></i> Add New Student</a>
        </div>
    </div>

    <form method="POST" class="bulk-actions" id="bulkForm">
        <div class="bulk-header">
            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            <label for="selectAll">Select All</label>
            <select name="bulk_action" class="bulk-select">
                <option value="activate">Activate Selected</option>
                <option value="deactivate">Deactivate Selected</option>
            </select>
            <button type="submit" class="bulk-apply-btn">Apply</button>
        </div>

        <!-- Student Table -->
        <div class="table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th width="30"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Semester</th>
                        <th>Class</th>
                        <th>Roll No</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($students_result && mysqli_num_rows($students_result) > 0): ?>
                        <?php while($student = mysqli_fetch_assoc($students_result)): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_students[]" value="<?= $student['id'] ?>" class="student-checkbox">
                                </td>
                                <td><?= $student['id'] ?></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($student['first_name'], 0, 1) . substr($student['last_name'], 0, 1)) ?>
                                        </div>
                                        <div class="student-details">
                                            <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                                            <small><?= htmlspecialchars($student['email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($student['username']) ?></td>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                                <td><?= htmlspecialchars($student['phone']) ?></td>
                                <td><?= htmlspecialchars($student['semester']) ?></td>
                                <td><?= htmlspecialchars($student['class']) ?></td>
                                <td><?= htmlspecialchars($student['roll_no']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $student['gender']=='Male'?'blue':($student['gender']=='Female'?'pink':'gray') ?>">
                                        <?= $student['gender'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $student['status']=='Active'?'success':'danger' ?>">
                                        <?= $student['status'] ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button type="button" class="btn-edit" onclick="openModal(<?= $student['id'] ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete_id=<?= $student['id'] ?>&search=<?= urlencode($search) ?>&page=<?= $page ?>&status=<?= urlencode($status_filter) ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="no-data">
                                <i class="fas fa-user-slash"></i> No students found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" class="page-link">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        <?php endif; ?>

        <div class="page-numbers">
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" class="page-number <?= $i==$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>" class="page-link">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Enhanced Edit Student Modal -->
<div id="editModal" class="modal">
    <div class="modal-content large-modal">
        <div class="form-container">
            <div class="modal-header">
                <h1><i class="fas fa-user-edit"></i> Edit Student</h1>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="editForm" class="student-form">
                <input type="hidden" name="id" id="student_id">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="page" value="<?= $page ?>">

                <div class="form-tabs">
                    <button type="button" class="tab-btn active" onclick="openTab('personal')">Personal Info</button>
                    <button type="button" class="tab-btn" onclick="openTab('academic')">Academic Info</button>
                    <button type="button" class="tab-btn" onclick="openTab('contact')">Contact Info</button>
                    <button type="button" class="tab-btn" onclick="openTab('security')">Security</button>
                </div>

                <!-- Personal Information Tab -->
                <div id="personal-tab" class="tab-content active">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" name="username" id="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" placeholder="Email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth">
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select name="gender" id="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                        <div class="form-group">
                            <label for="admission_date">Admission Date</label>
                            <input type="date" name="admission_date" id="admission_date">
                        </div>
                    </div>
                </div>

                <!-- Academic Information Tab -->
                <div id="academic-tab" class="tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="roll_no">Roll Number *</label>
                            <input type="text" name="roll_no" id="roll_no" placeholder="Roll Number" required>
                        </div>
                        <div class="form-group">
                            <label for="semester">Semester *</label>
                            <select name="semester" id="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="3rd">3rd Semester</option>
                                <option value="4th">4th Semester</option>
                                <option value="5th">5th Semester</option>
                                <option value="6th">6th Semester</option>
                                <option value="7th">7th Semester</option>
                                <option value="8th">8th Semester</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="class">Class *</label>
                            <input type="text" name="class" id="class" placeholder="Class" required>
                        </div>
                        <div class="form-group">
                            <label for="course">Course</label>
                            <input type="text" name="course" id="course" placeholder="Course">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" name="department" id="department" placeholder="Department">
                        </div>
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" id="status" required>
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Graduated">Graduated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Tab -->
                <div id="contact-tab" class="tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" name="phone" id="phone" placeholder="Phone Number">
                        </div>
                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" placeholder="Emergency Contact">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" placeholder="Full Address" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" id="city" placeholder="City">
                         </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" name="country" id="country" placeholder="Country">
                        </div>
                    </div>
                        <div class="form-group">
                            <label for="parent_phone">Parent/Guardian Phone</label>
                            <input type="text" name="parent_phone" id="parent_phone" placeholder="Parent/Guardian Phone">
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div id="security-tab" class="tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" placeholder="Leave blank to keep current password">
                            <small class="help-text">Minimum 8 characters with letters and numbers</small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <span class="strength-text" id="strengthText">Password strength</span>
                    </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="update_student" class="btn-submit"><i class="fas fa-save"></i> Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab functionality
function openTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show the selected tab content
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Activate the clicked tab button
    event.currentTarget.classList.add('active');
}

// Bulk selection
function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Enhanced modal opening with all fields
function openModal(id) {
    console.log("Opening modal for student ID:", id);
    
    fetch('get_student.php?id=' + id)
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if(data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            console.log("Student data received:", data);
            
            // Populate all form fields
            const fields = [
                'id', 'first_name', 'last_name', 'username', 'email', 'phone',
                'gender', 'semester', 'class', 'roll_no', 'status', 'date_of_birth',
                'address', 'city', 'parent_name','parent_phone', 'emergency_contact', 'admission_date',
                'course', 'department'
            ];
            
            fields.forEach(field => {
                if(document.getElementById(field)) {
                    document.getElementById(field).value = data[field] || '';
                }
            });
            
            // Show modal
            document.getElementById('editModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
            alert('Error loading student data: ' + error.message);
        });
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    // Reset form when closing
    document.getElementById('editForm').reset();
}

// Event listeners
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
        closeModal();
    }
};

// Password strength indicator
document.getElementById('password')?.addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    let text = 'Very Weak';
    let color = '#e74c3c';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;
    
    switch(strength) {
        case 1:
            text = 'Weak';
            color = '#e67e22';
            break;
        case 2:
            text = 'Fair';
            color = '#f1c40f';
            break;
        case 3:
            text = 'Good';
            color = '#2ecc71';
            break;
        case 4:
            text = 'Strong';
            color = '#27ae60';
            break;
    }
    
    if (strengthBar) {
        strengthBar.style.width = (strength * 25) + '%';
        strengthBar.style.backgroundColor = color;
    }
    if (strengthText) {
        strengthText.textContent = text;
        strengthText.style.color = color;
    }
});

// Form validation
document.getElementById('editForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password && password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (password && password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long!');
        return false;
    }
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>

</main>
</body>
</html>