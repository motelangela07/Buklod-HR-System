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

$conditions = ["archived = 0"];
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

// Additional stats
$totalEmployeesQuery = "SELECT COUNT(*) as total FROM employees WHERE archived = 0";
$totalEmployees = $conn->query($totalEmployeesQuery)->fetch_assoc()['total'];

$totalArchivedQuery = "SELECT COUNT(*) as total FROM employees WHERE archived = 1";
$totalArchived = $conn->query($totalArchivedQuery)->fetch_assoc()['total'];

$distinctDesignationsQuery = "SELECT COUNT(DISTINCT designation) as total FROM employees WHERE archived = 0";
$totalDesignations = $conn->query($distinctDesignationsQuery)->fetch_assoc()['total'];

// Get distinct designations for filter
$designationQuery = "SELECT DISTINCT designation FROM employees WHERE archived = 0";
$designationResult = $conn->query($designationQuery);
$designations = [];
while ($row = $designationResult->fetch_assoc()) {
    $designations[] = $row['designation'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buklod HR Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            padding: 10px 20px;
        }

        .header h2 {
            margin: 0;
            text-align: center;
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
            padding: 5px;
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

        .dashboard-boxes {
            display: flex;
            justify-content: space-around;
            margin: 20px auto;
            max-width: 1000px;
            gap: 20px;
        }

        .dashboard-box {
            flex: 1;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .dashboard-box:nth-child(1) {
            background-color: #28a745;
            color: white;
        }

        .dashboard-box:nth-child(2) {
            background-color: #007bff;
            color: white;
        }

        .dashboard-box:nth-child(3) {
            background-color: #dc3545;
            color: white;
        }

        .dashboard-box:hover {
            transform: translateY(-5px);
        }

        .dashboard-box:hover:nth-child(1) {
            background-color: #218838;
        }

        .dashboard-box:hover:nth-child(2) {
            background-color: #0056b3;
        }

        .dashboard-box:hover:nth-child(3) {
            background-color: #c82333;
        }

        .dashboard-box h4 {
            margin: 0;
            font-size: 16px;
            color: white;
        }

        .dashboard-box p {
            font-size: 24px;
            margin: 5px 0 0;
        }

        .dashboard-link {
            text-decoration: none;
            flex: 1;
        }

        .dashboard-link .dashboard-box {
            height: 70%;
        }

        .dashboard-summary {
            margin: 20px auto;
            padding: 20px;
            background: white;
            max-width: 1000px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
            background: #007bff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 13px;
        }

        .actions a.delete {
            background: #dc3545;
        }

        .actions a.print {
            background: #28a745;
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
        <h2>Buklod-Unlad Multi-Purpose Cooperative - HR System</h2>
    </div>

    <div class="top-bar">
        <form method="get" action="">
            <select name="designation">
                <option value="all" <?= $designation === 'all' ? 'selected' : '' ?>>All Designations</option>
                <?php foreach ($designations as $d): ?>
                    <option value="<?= $d ?>" <?= $designation === $d ? 'selected' : '' ?>><?= $d ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" placeholder="Search by name or number..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit">Apply</button>
        </form>
        <div class="top-links">
            <a href="add_employee.php">+ Add Employee</a>
            <a href="archived_employees.php">View Archived</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Dashboard Summary Boxes -->
    <div class="dashboard-boxes">
        <div class="dashboard-box">
            <h4>Total Employees</h4>
            <p><?= $totalEmployees; ?></p>
        </div>
        <div class="dashboard-box">
            <h4>Active Designations</h4>
            <p><?= $totalDesignations; ?></p>
        </div>
        <a href="archived_employees.php" class="dashboard-link">
            <div class="dashboard-box">
                <h4>Archived Employees</h4>
                <p><?= $totalArchived; ?></p>
            </div>
        </a>
    </div>

    <!-- Employee List -->
    <h3>Employee List</h3>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="employee-card">
            <img src="<?= !empty($row['profile_picture']) ? 'uploads/' . htmlspecialchars($row['profile_picture']) : 'default-avatar.png'; ?>" alt="Profile Picture">
            <div class="employee-info">
                <h4><?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name'] . ' ' . $row['middle_name']); ?></h4>
                <p>Employee No: <?= htmlspecialchars($row['employee_no']); ?></p>
                <p>Designation: <?= htmlspecialchars($row['designation']); ?></p>
            </div>
            <div class="actions">
                <a href="view_employee.php?id=<?= $row['id']; ?>">View</a>
                <a href="edit_employee.php?id=<?= $row['id']; ?>">Edit</a>
                <a class="delete" href="delete_employee.php?id=<?= $row['id']; ?>" onclick="return confirm('Archive this employee?')">Delete</a>
                <a class="print" href="print_employee.php?id=<?= $row['id']; ?>" target="_blank">Print</a>
            </div>
        </div>
    <?php endwhile; ?>

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
