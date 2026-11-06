<?php
require_once 'db.php';
include 'header.php';

// Access Check: Protect route[cite: 1, 3]
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Submitted Reports</h2>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Category</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['report_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['status']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['item_date']); ?></td>
                    <td>
                        <a href="edit_report.php?id=<?php echo $row['item_id']; ?>" class="btn" style="padding:4px 8px; font-size:0.8em;">Edit</a>
                        <a href="delete_report.php?id=<?php echo $row['item_id']; ?>" class="btn btn-danger" style="padding:4px 8px; font-size:0.8em;" onclick="return confirm('Delete this report?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">You have not submitted any reports yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
$stmt->close();
include 'footer.php'; 
?>