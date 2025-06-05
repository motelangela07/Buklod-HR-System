<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fields to collect data
    $fields = [
        'employee_no', 'first_name', 'middle_name', 'last_name', 'designation', 'date_hired', 'address',
        'birthday', 'status', 'height', 'weight', 'sss_no', 'pagibig_no', 'philhealth_no', 'tin',
        'blood_type', 'emergency_contact_no', 'personal_contact_no', 'resignation_termination', 'remarks'
    ];

    // Collecting form data
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = isset($_POST[$f]) ? $_POST[$f] : '';
    }

    // File handling for profile picture
    $file = $_FILES['profile_picture'];
    $image_name = null;

    if ($file['error'] == 0) {
        $image_name = time() . "_" . basename($file['name']);
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Validation for unique government IDs (SSS, Pag-IBIG, PhilHealth, TIN)
    $unique_checks = [
        'sss_no' => $data['sss_no'],
        'pagibig_no' => $data['pagibig_no'],
        'philhealth_no' => $data['philhealth_no'],
        'tin' => $data['tin']
    ];

    foreach ($unique_checks as $field => $value) {
        if (!empty($value)) {
            $query = "SELECT COUNT(*) as count FROM employees WHERE $field = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            if ($result['count'] > 0) {
                echo "<script>alert('The $field already exists in the database. Please use a unique ID.');</script>";
                exit();
            }
        }
    }

    // Prepare SQL statement
    $fields_str = implode(",", array_keys($data)) . ", profile_picture";
    $placeholders = rtrim(str_repeat("?,", count($data) + 1), ",");

    $stmt = $conn->prepare("INSERT INTO employees ($fields_str) VALUES ($placeholders)");
    $types = str_repeat("s", count($data)) . "s";
    $data_values = array_values($data);
    $data_values[] = $image_name;

    $stmt->bind_param($types, ...$data_values);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee - Buklod Unlad HR</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background-color: #f2f2f2; }
        .container { background: white; max-width: 650px; margin: auto; padding: 25px 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        h2 { text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], input[type="file"] {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;
        }
        .photo-preview { text-align: center; margin-bottom: 20px; }
        .photo-preview img { max-width: 150px; border-radius: 10px; margin-bottom: 10px; }
        button { width: 100%; padding: 10px; background-color: #0077cc; color: white; border: none; border-radius: 5px; font-size: 16px; margin-top: 10px; }
        button:hover { background-color: #005fa3; }
        .error { color: red; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Employee</h2>
    <form method="post" enctype="multipart/form-data" id="employeeForm" onsubmit="return validateForm()">
        <div class="photo-preview">
            <img id="previewImage" src="#" alt="Preview" style="display: none;">
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" required>
            </div>
        </div>

        <div class="form-group"><label for="employee_no">Employee No:</label><input type="text" name="employee_no" id="employee_no" required></div>
        <div class="form-group"><label for="first_name">First Name:</label><input type="text" name="first_name" id="first_name" required></div>
        <div class="form-group"><label for="middle_name">Middle Name:</label><input type="text" name="middle_name" id="middle_name" required></div>
        <div class="form-group"><label for="last_name">Last Name:</label><input type="text" name="last_name" id="last_name" required></div>

        <?php
$fields = [
    'designation' => 'Designation',
    'date_hired' => 'Date Hired',
    'address' => 'Address',
    'birthday' => 'Birthday',
    'status' => 'Status',
    'height' => 'Height',
    'weight' => 'Weight',
    'sss_no' => 'SSS No',
    'pagibig_no' => 'Pag-IBIG No',
    'philhealth_no' => 'PhilHealth No',
    'tin' => 'TIN',
    'blood_type' => 'Blood Type',
    'emergency_contact_name' => 'Emergency Contact Name', // corrected key and label
    'emergency_contact_no' => 'Emergency Contact No',
    'personal_contact_no' => 'Personal Contact No',
    'resignation_termination' => 'Resignation/Termination',
    'remarks' => 'Remarks'
];

foreach ($fields as $name => $label) {
    $type = in_array($name, ['date_hired', 'birthday']) ? 'date' : 'text';
    $required = in_array($name, ['resignation_termination', 'remarks']) ? '' : 'required';
    $maxlength = '';

    if ($name === 'personal_contact_no' || $name === 'emergency_contact_no') {
        $maxlength = 'maxlength="11"'; // both contact numbers should be 11 digits
    }

    echo "<div class='form-group'>
            <label for='$name'>$label</label>
            <input type='$type' name='$name' id='$name' $required $maxlength>
            <div id='{$name}-error' class='error'></div>
          </div>";
}
?>

<button type="submit">Save</button>
</form>
</div>


<script>
    document.getElementById("profile_picture").addEventListener("change", function(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById("previewImage");
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    function validateForm() {
        let isValid = true;

        // Validate contact numbers
        const emergencyContact = document.getElementById("emergency_contact_no").value;
        const personalContact = document.getElementById("personal_contact_no").value;

        // Validate personal contact (11 digits only)
        if (!/^\d{11}$/.test(personalContact)) {
            document.getElementById("personal_contact_no-error").innerText = "Personal contact must be exactly 11 digits.";
            isValid = false;
        } else {
            document.getElementById("personal_contact_no-error").innerText = "";
        }

        // Validate emergency contact (11 digits only)
        if (emergencyContact && !/^\d{11}$/.test(emergencyContact)) {
            document.getElementById("emergency_contact_no-error").innerText = "Emergency contact must be exactly 11 digits.";
            isValid = false;
        } else {
            document.getElementById("emergency_contact_no-error").innerText = "";
        }

        return isValid;
    }
</script>

</body>
</html>
