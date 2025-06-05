<?php
include 'db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE employees SET archived = 1 WHERE id = $id");
}
header("Location: dashboard.php");
exit();
?>
