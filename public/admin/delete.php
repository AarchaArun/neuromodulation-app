<?php

// starts current user session
// checks whether admin is logged in - only admin can delete the records
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!-- header layout for bootstrap -->
<?php require_once __DIR__ . '/../../header.php'; ?>

<?php
// database connection file

require_once __DIR__ . '/../../db.php';
$id = $_GET['id'] ?? 0;

if ($id) {
    $sql = "{CALL sp_DeleteForm(?)}";
    $params = [$id];
    sqlsrv_query($dbConnection, $sql, $params);
}

header("Location: index.php");
exit;
?>


<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
