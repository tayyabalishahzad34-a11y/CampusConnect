<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Campus Lost & Found Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">CampusConnect</a>
            <ul class="nav-links">
                <li><a href="index.php">Browse Listings</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="create_report.php">Create Report</a></li>
                    <li><a href="my_reports.php">My Reports</a></li>
                    <li><span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span></li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn-register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">