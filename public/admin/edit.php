<?php

// checks whether admin is logged in - only admin can edit the forms
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

// for Getting existing data
$sql = "{CALL sp_GetFormByID(?)}";
$params = [$id];
$stmt = sqlsrv_query($dbConnection, $sql, $params);
$form = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// for handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $FirstName = $_POST['first_name'] ?? '';
    $LastName  = $_POST['last_name'] ?? '';
    $DOB       = $_POST['dob'] ?? '';
    $Age       = $_POST['age'] ?? 0;
    $scores = [];
    for ($i = 1; $i <= 12; $i++) {
        $scores[$i] = (int)($_POST["q$i"] ?? 0);
    }
    $TotalScore = array_sum(array_slice($scores, 1));

    $sql = "{CALL sp_UpdateForm(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}";
    $params = [$id, $FirstName, $LastName, $DOB, $Age,
        $scores[1], $scores[2], $scores[3], $scores[4], $scores[5],
        $scores[6], $scores[7], $scores[8], $scores[9], $scores[10], $scores[11], $scores[12],
        $TotalScore
    ];

    $stmt = sqlsrv_query($dbConnection, $sql, $params);
    if ($stmt) {
        header("Location: view.php?id=$id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Form</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4 text-center">Edit Record</h3>
    <a href="view.php?id=<?= $form['ID'] ?>" class="btn btn-secondary mb-3">&larr; Cancel</a>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" value="<?= $form['FirstName'] ?>" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Surname</label>
                <input type="text" name="last_name" value="<?= $form['LastName'] ?>" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">DOB</label>
                <input type="date" name="dob" id="dob" value="<?= $form['DOB']->format('Y-m-d') ?>" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" id="age" value="<?= $form['Age'] ?>" class="form-control" readonly>
        </div>

        <?php for ($i = 1; $i <= 12; $i++): ?>
            <div class="mb-2">
                <label class="form-label">Q<?= $i ?></label>
                <input type="number" name="q<?= $i ?>" id="q<?= $i ?>" value="<?= $form["Q$i"] ?>" class="form-control question">
            </div>
        <?php endfor; ?>

        <div class="mb-3">
            <label class="form-label">Total Score</label>
            <input type="text" id="total_score" value="<?= $form['TotalScore'] ?>" class="form-control fw-bold" readonly>
        </div>

        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
    </form>
</div>

<script>
$("#dob").on("change", function() {
    const dob = new Date($(this).val());
    if (!isNaN(dob)) {
        const diff = Date.now() - dob.getTime();
        const ageDate = new Date(diff);
        const age = Math.abs(ageDate.getUTCFullYear() - 1970);
        $("#age").val(age);
    }
});

$(".question").on("input", function() {
    let total = 0;
    for (let i = 2; i <= 12; i++) {
        total += parseInt($("#q" + i).val()) || 0;
    }
    $("#total_score").val(total);
});
</script>
</body>
</html>

<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
