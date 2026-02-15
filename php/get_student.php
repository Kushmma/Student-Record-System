<?php
session_start();
include 'db.php';

// Only Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['error' => 'Access denied']));
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM students WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        header('Content-Type: application/json');
        echo json_encode($student);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Student not found']);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Student ID required']);
}
?>