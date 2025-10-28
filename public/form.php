
<!-- header layout for bootstrap -->
<?php require_once __DIR__ . '/../header.php'; ?>

<?php

// for connecting to database
require_once __DIR__ . '/../db.php'; 

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // for Collecting inputs safely
    $FirstName = $_POST['first_name'] ?? '';
    $LastName  = $_POST['last_name'] ?? '';
    $DOB       = $_POST['dob'] ?? '';
    $Age       = $_POST['age'] ?? 0;

    //for  Collecting question scores
    $scores = [];
    for ($i = 1; $i <= 12; $i++) {
        $scores[$i] = (int)($_POST["q$i"] ?? 0);
    }

    // for Calculating total score (from Q2–Q12 only)
    $TotalScore = array_sum(array_slice($scores, 1));

    // for Calling stored procedure
    $sql = "{CALL sp_AddForm(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)}";
    $params = [
        $FirstName, $LastName, $DOB, $Age,
        $scores[1], $scores[2], $scores[3], $scores[4], $scores[5], $scores[6],
        $scores[7], $scores[8], $scores[9], $scores[10], $scores[11], $scores[12],
        $TotalScore
    ];

    $stmt = sqlsrv_query($dbConnection, $sql, $params);
    if ($stmt === false) {
        $message = "<div class='alert alert-danger'>Error saving form. Please check connection.</div>";
    } else {
        $message = "<div class='alert alert-success'>Form submitted successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuromodulation Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4 text-center">Neuromodulation Form</h2>
    <?= $message ?>

    <form method="POST" id="neuromodulationForm" class="card p-4 shadow-sm">

        <!-- Patient Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Patient Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Surname</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" id="age" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brief Pain Inventory Questions  -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">Brief Pain Inventory (BPI)</div>
            <div class="card-body">
                <?php
                $questions = [
                    1 => "How much relief have pain treatments or medications FROM THIS CLINIC provided? (0–100)",
                    2 => "Pain at its WORST in the past week (0–10)",
                    3 => "Pain at its LEAST in the past week (0–10)",
                    4 => "Pain on the AVERAGE (0–10)",
                    5 => "Pain RIGHT NOW (0–10)",
                    6 => "Pain interfered with: General Activity (0–10)",
                    7 => "Pain interfered with: Mood (0–10)",
                    8 => "Pain interfered with: Walking ability (0–10)",
                    9 => "Pain interfered with: Normal work (0–10)",
                    10 => "Pain interfered with: Relationships with others (0–10)",
                    11 => "Pain interfered with: Sleep (0–10)",
                    12 => "Pain interfered with: Enjoyment of life (0–10)",
                ];

                foreach ($questions as $num => $text) {
                    $max = ($num === 1) ? 100 : 10;
                    echo "
                    <div class='mb-3'>
                        <label class='form-label'>$num. $text</label>
                        <input type='number' min='0' max='$max' name='q$num' id='q$num' class='form-control question' required>
                    </div>";
                }
                ?>
            </div>
        </div>

        <!--for calculating Total Score -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">Total Score</div>
            <div class="card-body">
                <input type="text" name="total_score" id="total_score" class="form-control text-center fw-bold" readonly>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success px-5">Submit</button>
        </div>
    </form>
</div>

<script>
// for Auto-calculating age based on date of birth
$("#dob").on("change", function() {
    const dob = new Date($(this).val());
    if (!isNaN(dob)) {
        const diff = Date.now() - dob.getTime();
        const ageDate = new Date(diff);
        const age = Math.abs(ageDate.getUTCFullYear() - 1970);
        $("#age").val(age);
    }
});

// for Auto-total calculation (scores calculated from Q2–Q12 only)
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
<?php require_once __DIR__ . '/../footer.php'; ?>
