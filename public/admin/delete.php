<?php
// checks whether admin is logged in - only admin can delete the record
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: login.php"); exit;
}

require_once __DIR__ . '/../../db.php';

// Accept POST (preferred) or GET (fallback)
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

if ($id <= 0) {
    header('Location: /admin/index.php?deleted=0'); exit;
}

$sqlDelete = "{CALL delete_PatientForm(?)}";
$params = [$id];
$stmt = sqlsrv_query($dbConnection, $sqlDelete, $params);

if ($stmt === false) {
   
    header('Location: /admin/index.php?deleted=0'); exit;
}

// Check rows affected to ensure something was actually deleted
$rows = sqlsrv_rows_affected($stmt);
if ($rows === false || $rows < 1) {
    header('Location: /admin/index.php?deleted=0'); exit;
}

header('Location: /admin/index.php?deleted=1'); exit;
