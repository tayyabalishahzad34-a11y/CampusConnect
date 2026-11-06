<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access Check & Ownership Check[cite: 1, 2, 3]
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Check image path before deleting record
$stmt = $conn->prepare("SELECT image_path FROM items WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (!empty($row['image_path']) && file_exists($row['image_path'])) {
        unlink($row['image_path']); // Remove file from server storage
    }

    $delete_stmt = $conn->prepare("DELETE FROM items WHERE item_id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $item_id, $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

$stmt->close();
header("Location: my_reports.php");
exit();
?>