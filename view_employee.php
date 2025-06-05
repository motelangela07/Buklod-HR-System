<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

if (!isset($_GET['id'])) {
    echo "Employee ID is missing.";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Employee not found.";
    exit();
}

$employee = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Employee - Buklod Unlad HR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f4f4;
        }

        .container {
            background: white;
            max-width: 750px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .photo {
            text-align: center;
            margin-bottom: 25px;
        }

        .photo img {
            width: 160px;
            height: 160px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #0077cc;
            color: white;
            width: 35%;
            text-transform: capitalize;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 22px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Employee Details</h2>

    <div class="photo">
        <img src="<?= !empty($employee['profile_picture']) ? 'uploads/' . htmlspecialchars($employee['profile_picture']) : 'default-avatar.png'; ?>"
             alt="Profile Picture"
             onerror="this.onerror=null;this.src='default-avatar.png';">
    </div>

    <table>
        <tr>
            <th>Full Name</th>
            <td><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name']) ?></td>
        </tr>

        <?php
        $excluded = ['id', 'first_name', 'middle_name', 'last_name', 'profile_picture'];
        foreach ($employee as $key => $value):
            if (in_array($key, $excluded)) continue;
            $label = ucwords(str_replace("_", " ", $key));
        ?>
            <tr>
                <th><?= htmlspecialchars($label) ?></th>
                <td><?= htmlspecialchars($value) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
