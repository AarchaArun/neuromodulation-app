<?php

// checks whether admin is logged in - only admin can view the form
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!-- header layout for bootstrap -->
<?php require_once __DIR__ . '/../../header.php'; ?>

<?php
require_once __DIR__ . '/../../db.php';

$id = $_GET['id'] ?? 0;

// for fetching selected record details using stored procedure
$sql = "{CALL sp_GetFormByID(?)}";
$params = [$id];
$stmt = sqlsrv_query($dbConnection, $sql, $params);

if (!$stmt || !($form = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
    die("<div class='alert alert-danger text-center m-5'>Record not found.</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4 text-center">Patient Record - Read Only</h3>
    <a href="index.php" class="btn btn-secondary mb-3">&larr; Back to Admin</a>

    <div class="card shadow-sm p-4">
        <p><strong>Name:</strong> <?= $form['FirstName'] . ' ' . $form['LastName'] ?></p>
        <p><strong>Date of Birth:</strong> <?= $form['DOB']->format('Y-m-d') ?></p>
        <p><strong>Age:</strong> <?= $form['Age'] ?></p>
        <p><strong>Total Score:</strong> <?= $form['TotalScore'] ?></p>
        <p><strong>Date Submitted:</strong> <?= $form['DateSubmitted']->format('Y-m-d H:i') ?></p>

        <div class="text-center mt-3">
            <a href="edit.php?id=<?= $form['ID'] ?>" class="btn btn-primary">Edit</a>
            <a href="delete.php?id=<?= $form['ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
        </div>
    </div>
</div>
</body>
</html>

<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
