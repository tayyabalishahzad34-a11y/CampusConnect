<?php
require_once 'db.php';
include 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT i.*, u.full_name, u.email, u.phone FROM items i JOIN users u ON i.user_id = u.user_id WHERE i.item_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-error'>Item not found.</div>";
    include 'footer.php';
    exit();
}

$item = $result->fetch_assoc();
?>

<div class="form-card" style="max-width: 700px;">
    <h2><?php echo htmlspecialchars($item['title']); ?></h2>
    <p>
        <span class="badge <?php echo $item['report_type'] === 'Lost' ? 'badge-lost' : 'badge-found'; ?>"><?php echo htmlspecialchars($item['report_type']); ?></span>
        <span class="badge badge-status">Status: <?php echo htmlspecialchars($item['status']); ?></span>
    </p>

    <?php if ($item['image_path']): ?>
        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" style="width:100%; max-height:350px; object-fit:cover; border-radius:6px; margin: 15px 0;">
    <?php endif; ?>

    <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($item['item_date']); ?></p>
    <p><strong>Description:</strong></p>
    <p style="background:#f1f5f9; padding:10px; border-radius:4px;"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>

    <hr style="margin: 20px 0;">

    <h3>Contact Reporter</h3>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p><strong>Reporter Name:</strong> <?php echo htmlspecialchars($item['full_name']); ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($item['email']); ?>"><?php echo htmlspecialchars($item['email']); ?></a></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($item['phone'] ?: 'N/A'); ?></p>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to view contact details and reach out to the poster.</p>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
include 'footer.php'; 
?>