<?php
include 'db.php';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $employee = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Employee</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 40px;
            color: #333;
        }
        .resume {
            max-width: 900px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #004080;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #aaa;
        }
        .section {
            margin-top: 30px;
        }
        .section h2 {
            margin-bottom: 10px;
            font-size: 20px;
            border-bottom: 2px solid #004080;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 200px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }

        @media print {
            body {
                margin: 0;
            }
            .resume {
                border: none;
                padding: 0;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>
    <div class="resume">
        <div class="header">
            <div>
                <h1><?= htmlspecialchars($employee['first_name']) . ' ' . htmlspecialchars($employee['middle_name']) . ' ' . htmlspecialchars($employee['last_name']); ?></h1>
                <p><?= htmlspecialchars($employee['designation']); ?></p>
            </div>
            <div>
                <img class="profile-pic" src="uploads/<?= htmlspecialchars($employee['profile_picture']); ?>" alt="Profile Picture">
            </div>
        </div>

        <div class="section">
            <h2>Personal Information</h2>
            <div class="info-row"><div class="info-label">Employee No:</div><div class="info-value"><?= $employee['employee_no']; ?></div></div>
            <div class="info-row"><div class="info-label">Date Hired:</div><div class="info-value"><?= $employee['date_hired']; ?></div></div>
            <div class="info-row"><div class="info-label">Birthday:</div><div class="info-value"><?= $employee['birthday']; ?></div></div>
            <div class="info-row"><div class="info-label">Address:</div><div class="info-value"><?= $employee['address']; ?></div></div>
            <div class="info-row"><div class="info-label">Status:</div><div class="info-value"><?= $employee['status']; ?></div></div>
        </div>

        <div class="section">
            <h2>Physical Attributes</h2>
            <div class="info-row"><div class="info-label">Height:</div><div class="info-value"><?= $employee['height']; ?></div></div>
            <div class="info-row"><div class="info-label">Weight:</div><div class="info-value"><?= $employee['weight']; ?></div></div>
            <div class="info-row"><div class="info-label">Blood Type:</div><div class="info-value"><?= $employee['blood_type']; ?></div></div>
        </div>

        <div class="section">
            <h2>Government IDs</h2>
            <div class="info-row"><div class="info-label">SSS No:</div><div class="info-value"><?= $employee['sss_no']; ?></div></div>
            <div class="info-row"><div class="info-label">Pag-IBIG No:</div><div class="info-value"><?= $employee['pagibig_no']; ?></div></div>
            <div class="info-row"><div class="info-label">PhilHealth No:</div><div class="info-value"><?= $employee['philhealth_no']; ?></div></div>
            <div class="info-row"><div class="info-label">TIN:</div><div class="info-value"><?= $employee['tin']; ?></div></div>
        </div>

        <div class="section">
            <h2>Contact Information</h2>
            <div class="info-row"><div class="info-label">Emergency Contact Name:</div><div class="info-value"><?= $employee['emergency_contact_name']; ?></div></div>
            <div class="info-row"><div class="info-label">Emergency Contact No:</div><div class="info-value"><?= $employee['emergency_contact_no']; ?></div></div>
            <div class="info-row"><div class="info-label">Personal Contact No:</div><div class="info-value"><?= $employee['personal_contact_no']; ?></div></div>
        </div>

        <div class="section">
            <h2>Other Information</h2>
            <div class="info-row"><div class="info-label">Resignation/Termination:</div><div class="info-value"><?= $employee['resignation_termination']; ?></div></div>
            <div class="info-row"><div class="info-label">Remarks:</div><div class="info-value"><?= $employee['remarks']; ?></div></div>
        </div>
    </div>
</body>
</html>
