<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [
        'employee_no', 'first_name', 'middle_name', 'last_name',
        'designation', 'date_hired', 'address', 'birthday', 'status',
        'height', 'weight', 'sss_no', 'pagibig_no', 'philhealth_no',
        'tin', 'blood_type', 'emergency_contact_name', 'emergency_contact_no', 'personal_contact_no',
        'resignation_termination', 'remarks'
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f]);
    }

    $id = intval($_GET['id']);

    // Fetch current profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM employees WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $imageFilename = $row['profile_picture'];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Ensure uploads folder exists
        }
        $imageFilename = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $targetFile = $targetDir . $imageFilename;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            // New image uploaded successfully
        } else {
            echo "Error uploading image.";
            exit();
        }
    }

    // Update employee record
    $query = "UPDATE employees SET 
        employee_no=?, first_name=?, middle_name=?, last_name=?, 
        designation=?, date_hired=?, address=?, birthday=?, status=?, 
        height=?, weight=?, sss_no=?, pagibig_no=?, philhealth_no=?, 
        tin=?, blood_type=?, emergency_contact_name=?, emergency_contact_no=?, personal_contact_no=?, 
        resignation_termination=?, remarks=?, profile_picture=? 
        WHERE id=?";

$stmt = $conn->prepare($query);
$stmt->bind_param(
    "ssssssssssssssssssssssi", // 22 strings + 1 integer = 23
    $data['employee_no'],
    $data['first_name'],
    $data['middle_name'],
    $data['last_name'],
    $data['designation'],
    $data['date_hired'],
    $data['address'],
    $data['birthday'],
    $data['status'],
    $data['height'],
    $data['weight'],
    $data['sss_no'],
    $data['pagibig_no'],
    $data['philhealth_no'],
    $data['tin'],
    $data['blood_type'],
    $data['emergency_contact_name'],
    $data['emergency_contact_no'],
    $data['personal_contact_no'],
    $data['resignation_termination'],
    $data['remarks'],
    $imageFilename,
    $id
);
$stmt->execute();


    header("Location: dashboard.php");
    exit();
}

// Fetch employee data for form
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id=?");
    $id = intval($_GET['id']);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $employee = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            gap: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .photo {
            flex: 1;
            text-align: center;
        }
        .photo img {
            width: 150px;
            height: 150px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #ccc;
        }
        .form-container {
            flex: 2;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #0077cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #005fa3;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="photo">
        <?php if (!empty($employee['profile_picture'])): ?>
            <img src="uploads/<?= htmlspecialchars($employee['profile_picture']) ?>" alt="Profile Picture">
        <?php else: ?>
            <img src="default-avatar.png" alt="No Image">
        <?php endif; ?>
    </div>

    <div class="form-container">
        <h2>Edit Employee</h2>
        <form method="post" enctype="multipart/form-data">
            <?php
            function input($name, $label, $value, $type = "text", $extra = "") {
                echo "<div class='form-group'>
                        <label for='$name'>$label:</label>
                        <input type='$type' name='$name' id='$name' value='" . htmlspecialchars($value) . "' required $extra>
                      </div>";
            }

            input("employee_no", "Employee No", $employee['employee_no']);
            input("first_name", "First Name", $employee['first_name']);
            input("middle_name", "Middle Name", $employee['middle_name']);
            input("last_name", "Last Name", $employee['last_name']);
            input("designation", "Designation", $employee['designation']);
            input("date_hired", "Date Hired", $employee['date_hired'], "date");
            input("address", "Address", $employee['address']);
            input("birthday", "Birthday", $employee['birthday'], "date");
            input("status", "Status", $employee['status']);
            input("height", "Height", $employee['height']);
            input("weight", "Weight", $employee['weight']);
            input("sss_no", "SSS No", $employee['sss_no']);
            input("pagibig_no", "Pag-IBIG No", $employee['pagibig_no']);
            input("philhealth_no", "PhilHealth No", $employee['philhealth_no']);
            input("tin", "TIN", $employee['tin']);
            input("blood_type", "Blood Type", $employee['blood_type']);
            input("emergency_contact_name", "Emergency Contact Name", $employee['emergency_contact_name']);
            input("emergency_contact_no", "Emergency Contact No", $employee['emergency_contact_no'], "text", "maxlength='11' pattern='[0-9]{11}'");
            input("personal_contact_no", "Personal Contact No", $employee['personal_contact_no'], "text", "maxlength='11' pattern='[0-9]{11}'");
            input("resignation_termination", "Resignation/Termination", $employee['resignation_termination']);
            input("remarks", "Remarks", $employee['remarks']);
            ?>

            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" accept="image/*">
            </div>

            <br>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

</body>
</html>
