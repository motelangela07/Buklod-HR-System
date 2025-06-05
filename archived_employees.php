<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';

// Filters and pagination
$designation = $_GET['designation'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$conditions = ["archived = 1"];
if ($designation !== 'all') {
    $designationEscaped = $conn->real_escape_string($designation);
    $conditions[] = "designation = '$designationEscaped'";
}
if (!empty($search)) {
    $searchEscaped = $conn->real_escape_string($search);
    $conditions[] = "(first_name LIKE '%$searchEscaped%' OR last_name LIKE '%$searchEscaped%' OR employee_no LIKE '%$searchEscaped%')";
}
$whereClause = 'WHERE ' . implode(' AND ', $conditions);

$countQuery = "SELECT COUNT(*) as total FROM employees $whereClause";
$countResult = $conn->query($countQuery);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

$query = "SELECT * FROM employees $whereClause ORDER BY last_name ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Employees - Buklod Unlad HR</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
        }
        .header h2 {
            margin: 0;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #e9ecef;
            padding: 10px 20px;
        }
        .top-bar form {
            display: flex;
            gap: 10px;
        }
        .top-bar input, .top-bar select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .top-bar button {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .top-links a {
            text-decoration: none;
            margin-left: 10px;
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
        }
        h3 {
            margin: 20px;
            color: #333;
        }
        .employee-card {
            background: white;
            margin: 15px auto;
            padding: 15px;
            border-radius: 10px;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        .employee-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
        }
        .employee-info {
            flex: 1;
        }
        .employee-info h4 {
            margin: 0;
            font-size: 18px;
        }
        .employee-info p {
            margin: 5px 0;
            color: #555;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: white;
            background: #17a2b8;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
        }
        .actions a.delete {
            background: #dc3545;
        }
        .pagination {
            text-align: center;
            margin: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Buklod-Unlad Multi-Purpose Cooperative - Archived Employees</h2>
    </div>

    <div class="top-bar">
        <form method="get" action="">
            <select name="designation">
                <option value="all" <?= $designation === 'all' ? 'selected' : '' ?>>All Designations</option>
                <option value="Accounting" <?= $designation === 'Accounting' ? 'selected' : '' ?>>Accounting</option>
                <option value="Frontdesk" <?= $designation === 'Frontdesk' ? 'selected' : '' ?>>Frontdesk</option>
                <option value="Employee" <?= $designation === 'Employee' ? 'selected' : '' ?>>Employee</option>
                <option value="Educ Committee" <?= $designation === 'Educ Committee' ? 'selected' : '' ?>>Educ Committee</option>
                <option value="Audit Committee" <?= $designation === 'Audit Committee' ? 'selected' : '' ?>>Audit Committee</option>
                <option value="Credit Committee" <?= $designation === 'Credit Committee' ? 'selected' : '' ?>>Credit Committee</option>
                <option value="Election Committee" <?= $designation === 'Election Committee' ? 'selected' : '' ?>>Election Committee</option>
                <option value="Board Member" <?= $designation === 'Board Member' ? 'selected' : '' ?>>Board Member</option>
            </select>
            <input type="text" name="search" placeholder="Search by name or number..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit">Apply</button>
        </form>
        <div class="top-links">
            <a href="dashboard.php">← Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h3>Archived Employees</h3>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="employee-card">
            <img src="<?= !empty($row['profile_picture']) ? 'uploads/' . htmlspecialchars($row['profile_picture']) : 'default-avatar.png'; ?>" alt="Profile Picture">
            <div class="employee-info">
                <h4><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?></h4>
                <p>Employee No: <?= htmlspecialchars($row['employee_no']); ?></p>
                <p>Designation: <?= htmlspecialchars($row['designation']); ?></p>
            </div>
            <div class="actions">
                <a href="restore_employee.php?id=<?= $row['id']; ?>" onclick="return confirm('Restore this employee?')">Restore</a>
                <a class="delete" href="delete_permanent.php?id=<?= $row['id']; ?>" onclick="return confirm('Permanently delete this employee?')">Delete Permanently</a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: #888;">No archived employees found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« Prev</a>
        <?php endif; ?>
        Page <?= $page ?> of <?= $totalPages ?>
        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next »</a>
        <?php endif; ?>
    </div>

</body>
</html>
