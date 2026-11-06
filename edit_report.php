<?php
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$error = '';

// Security Check: Ensure item exists and belongs to the logged-in user[cite: 1, 2, 3]
$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-error'>Report not found or access denied.</div>";
    include 'footer.php';
    exit();
}

$item = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $item_date = $_POST['item_date'];
    $report_type = $_POST['report_type'];
    $status = $_POST['status'];

    $update_stmt = $conn->prepare("UPDATE items SET title=?, category=?, description=?, location=?, item_date=?, report_type=?, status=? WHERE item_id=? AND user_id=?");
    $update_stmt->bind_param("sssssssii", $title, $category, $description, $location, $item_date, $report_type, $status, $item_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: my_reports.php");
        exit();
    } else {
        $error = "Failed to update record.";
    }
    $update_stmt->close();
}
?>

<div class="form-card" style="max-width: 600px;">
    <h2>Edit Report</h2>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <form method="POST" action="edit_report.php?id=<?php echo $item_id; ?>">
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="Active" <?php if ($item['status'] == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Recovered" <?php if ($item['status'] == 'Recovered') echo 'selected'; ?>>Recovered</option>
                <option value="Returned" <?php if ($item['status'] == 'Returned') echo 'selected'; ?>>Returned</option>
            </select>
        </div>
        <div class="form-group">
            <label>Report Type</label>
            <select name="report_type" required>
                <option value="Lost" <?php if ($item['report_type'] == 'Lost') echo 'selected'; ?>>Lost</option>
                <option value="Found" <?php if ($item['report_type'] == 'Found') echo 'selected'; ?>>Found</option>
            </select>
        </div>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <option value="Electronics" <?php if ($item['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                <option value="Documents" <?php if ($item['category'] == 'Documents') echo 'selected'; ?>>Documents/IDs</option>
                <option value="Clothing" <?php if ($item['category'] == 'Clothing') echo 'selected'; ?>>Clothing</option>
                <option value="Keys" <?php if ($item['category'] == 'Keys') echo 'selected'; ?>>Keys</option>
                <option value="Other" <?php if ($item['category'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($item['location']); ?>" required>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="item_date" value="<?php echo htmlspecialchars($item['item_date']); ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
        </div>
        <button type="submit" class="btn">Update Report</button>
    </form>
</div>

<?php 
$stmt->close();
include 'footer.php'; 
?>