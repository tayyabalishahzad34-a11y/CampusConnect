<?php
require_once 'db.php';
include 'header.php';

// Retrieve search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$report_type = isset($_GET['report_type']) ? trim($_GET['report_type']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build dynamic search query using prepared statements[cite: 1, 3]
$sql = "SELECT i.*, u.full_name FROM items i JOIN users u ON i.user_id = u.user_id WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND (i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if ($category !== '') {
    $sql .= " AND i.category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($report_type !== '') {
    $sql .= " AND i.report_type = ?";
    $params[] = $report_type;
    $types .= "s";
}

if ($status !== '') {
    $sql .= " AND i.status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Campus Listings</h2>

<!-- Search & Filter Form -->
<form method="GET" action="index.php" class="filter-card">
    <input type="text" name="search" placeholder="Search keywords..." value="<?php echo htmlspecialchars($search); ?>">
    
    <select name="category">
        <option value="">All Categories</option>
        <option value="Electronics" <?php if ($category == 'Electronics') echo 'selected'; ?>>Electronics</option>
        <option value="Documents" <?php if ($category == 'Documents') echo 'selected'; ?>>Documents/IDs</option>
        <option value="Clothing" <?php if ($category == 'Clothing') echo 'selected'; ?>>Clothing</option>
        <option value="Keys" <?php if ($category == 'Keys') echo 'selected'; ?>>Keys</option>
        <option value="Other" <?php if ($category == 'Other') echo 'selected'; ?>>Other</option>
    </select>

    <select name="report_type">
        <option value="">All Types</option>
        <option value="Lost" <?php if ($report_type == 'Lost') echo 'selected'; ?>>Lost</option>
        <option value="Found" <?php if ($report_type == 'Found') echo 'selected'; ?>>Found</option>
    </select>

    <select name="status">
        <option value="">All Statuses</option>
        <option value="Active" <?php if ($status == 'Active') echo 'selected'; ?>>Active</option>
        <option value="Recovered" <?php if ($status == 'Recovered') echo 'selected'; ?>>Recovered</option>
        <option value="Returned" <?php if ($status == 'Returned') echo 'selected'; ?>>Returned</option>
    </select>

    <button type="submit" class="btn">Filter</button>
    <a href="index.php" class="btn btn-secondary">Reset</a>
</form>

<div class="grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <?php if ($row['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Item Image">
                <?php else: ?>
                    <img src="https://via.placeholder.com/300x180?text=No+Image" alt="No Image">
                <?php endif; ?>
                <div class="card-body">
                    <span class="badge <?php echo $row['report_type'] === 'Lost' ? 'badge-lost' : 'badge-found'; ?>">
                        <?php echo htmlspecialchars($row['report_type']); ?>
                    </span>
                    <span class="badge badge-status"><?php echo htmlspecialchars($row['status']); ?></span>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['item_date']); ?></p>
                    <p><a href="item.php?id=<?php echo $row['item_id']; ?>" class="btn" style="margin-top:10px;">View Details</a></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No listings found matching your search criteria.</p>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
include 'footer.php'; 
?>