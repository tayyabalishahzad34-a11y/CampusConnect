<?php
require_once 'db.php';
include 'header.php';

// Route Protection: Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $item_date = $_POST['item_date'];
    $report_type = $_POST['report_type'];
    $user_id = $_SESSION['user_id'];
    $image_path = NULL;

    // Handle Image Upload safely[cite: 1, 2, 3]
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $dest_path;
            }
        } else {
            $error = "Allowed image types: jpg, jpeg, png, gif.";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO items (user_id, title, category, description, location, item_date, report_type, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $title, $category, $description, $location, $item_date, $report_type, $image_path);

        if ($stmt->execute()) {
            header("Location: my_reports.php");
            exit();
        } else {
            $error = "Error saving report. Please try again.";
        }
        $stmt->close();
    }
}
?>

<div class="form-card" style="max-width: 600px;">
    <h2>Create Lost / Found Report</h2>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <form method="POST" action="create_report.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Report Type *</label>
            <select name="report_type" required>
                <option value="Lost">Lost</option>
                <option value="Found">Found</option>
            </select>
        </div>
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" placeholder="e.g. Blue Student ID Card" required>
        </div>
        <div class="form-group">
            <label>Category *</label>
            <select name="category" required>
                <option value="Electronics">Electronics</option>
                <option value="Documents">Documents/IDs</option>
                <option value="Clothing">Clothing</option>
                <option value="Keys">Keys</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Location *</label>
            <input type="text" name="location" placeholder="e.g. Main Library 2nd Floor" required>
        </div>
        <div class="form-group">
            <label>Date Lost/Found *</label>
            <input type="date" name="item_date" required>
        </div>
        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Upload Image (Optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn">Submit Report</button>
    </form>
</div>

<?php include 'footer.php'; ?>