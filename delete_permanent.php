<?php
include 'db.php'; // make sure this connects to your database

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // make sure id is an integer

    // First, you can optionally check if the record exists
    $check = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Now, delete the employee
        $delete = $conn->prepare("DELETE FROM employees WHERE id = ?");
        $delete->bind_param("i", $id);
        if ($delete->execute()) {
            echo "<script>alert('Employee record permanently deleted.'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting the record.'); window.location.href='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Employee not found.'); window.location.href='dashboard.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='dashboard.php';</script>";
}
?>
