<?php
include 'db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE employees SET archived = 0 WHERE id = $id");
}
header("Location: archived_employees.php");
exit();
?>
