<?php
include 'db.php';

$result = $conn->query("SELECT * FROM employees ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print All Employees</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
    </style>
    <script>
        window.onload = function() { window.print(); }
    </script>
</head>
<body>
    <h2>All Employee Records</h2>
    <table>
        <tr>
            <th>Picture</th>
            <th>Employee No</th>
            <th>Name</th>
            <th>Designation</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="uploads/<?= $row['profile_picture']; ?>" width="50" height="50"></td>
            <td><?= $row['employee_no']; ?></td>
            <td><?= $row['name']; ?></td>
            <td><?= $row['designation']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
