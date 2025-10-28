<?php

// checks whether admin is logged in - only admin can view all submitted forms
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

// for Getting all forms
$sql = "{CALL sp_GetForms}";
$stmt = sqlsrv_query($dbConnection, $sql);

if ($stmt === false) {
    die("<div class='alert alert-danger'>Error loading records: " . print_r(sqlsrv_errors(), true) . "</div>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Neuromodulation Forms</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4 text-center">Admin Panel - Completed Forms</h2>
    <a href="../form.php" class="btn btn-secondary mb-3">+ New Form</a>

    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>Date of Submission</th>
                <th>First Name</th>
                <th>Surname</th>
                <th>Age</th>
                <th>Date of Birth</th>
                <th>Total Score</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
            <tr onclick="window.location='view.php?id=<?= $row['ID'] ?>'">
                <td><?= $row['DateSubmitted']->format('Y-m-d H:i') ?></td>
                <td><?= htmlspecialchars($row['FirstName']) ?></td>
                <td><?= htmlspecialchars($row['LastName']) ?></td>
                <td><?= htmlspecialchars($row['Age']) ?></td>
                <td><?= $row['DOB']->format('Y-m-d') ?></td>
                <td><strong><?= htmlspecialchars($row['TotalScore']) ?></strong></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
