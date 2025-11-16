<?php
//checks whether admin is logged in - only admin can edit the record
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../header.php';
require_once __DIR__ . '/../../db.php';

/**
 * Safely format SQLSRV date/datetime values which may come as DateTime objects or strings.
 */
function fmtDate($v, string $pattern = 'Y-m-d'): string {
    if ($v instanceof DateTimeInterface) return $v->format($pattern);
    if (is_string($v) && $v !== '') {
        $ts = strtotime($v);
        return $ts ? date($pattern, $ts) : '';
    }
    return '';
}

// Load ID from query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /admin/index.php');
    exit;
}

// Handle POST (save)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $dob        = trim($_POST['dob'] ?? '');  // 'YYYY-MM-DD'
    $age        = (int)($_POST['age'] ?? 0);

    // Questions Q1–Q12
    $q = [];
    for ($i = 1; $i <= 12; $i++) {
        $q[$i] = isset($_POST["q$i"]) && $_POST["q$i"] !== '' ? (int)$_POST["q$i"] : 0;
    }

    // Total score is Q2–Q12
    $totalScore = 0;
    for ($i = 2; $i <= 12; $i++) {
        $totalScore += $q[$i];
    }

    // 18 placeholders to match update_PatientForm proc
    $sql = "{CALL update_PatientForm(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}";
    $params = [
        $id,
        $firstName,
        $lastName,
        $dob,
        $age,
        $q[1], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $q[11], $q[12],
        $totalScore
    ];

    $result = sqlsrv_query($dbConnection, $sql, $params);

    if ($result === false) {
        // TEMP: show error to debug if needed; you can comment this out once it works
        echo "<div class='alert alert-danger'>Unable to save changes.</div>";
        echo "<pre>" . print_r(sqlsrv_errors(), true) . "</pre>";
    } else {
        header('Location: /admin/index.php?updated=1');
        exit;
    }
}

// GET: load existing record for editing
$sqlGet = "{CALL get_PatientFormById(?)}";
$params = [$id];
$res = sqlsrv_query($dbConnection, $sqlGet, $params);
if ($res === false) {
    echo "<div class='alert alert-danger'>Unable to load record.</div>";
    require_once __DIR__ . '/../../footer.php';
    exit;
}
$record = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
if (!$record) {
    echo "<div class='alert alert-warning'>Record not found.</div>";
    require_once __DIR__ . '/../../footer.php';
    exit;
}

// Pre-fill values from record
$firstName  = (string)($record['FirstName'] ?? '');
$lastName   = (string)($record['LastName'] ?? '');
$dobStr     = fmtDate($record['DOB'] ?? null, 'Y-m-d');
$ageVal     = (int)($record['Age'] ?? 0);

$qVals = [];
for ($i = 1; $i <= 12; $i++) {
    $qVals[$i] = isset($record['Q' . $i]) ? (int)$record['Q' . $i] : 0;
}
$totalScore = (int)($record['TotalScore'] ?? 0);

// Question texts
$questions = [
    1  => "How much relief have pain treatments or medications FROM THIS CLINIC provided? (0–100)",
    2  => "Please rate your pain at its WORST in the past week. (0–10)",
    3  => "Please rate your pain at its LEAST in the past week. (0–10)",
    4  => "Please rate your pain on the AVERAGE. (0–10)",
    5  => "How much pain do you have RIGHT NOW? (0–10)",
    6  => "How much has pain interfered with your: General activity. (0–10)",
    7  => "How much has pain interfered with your: Mood. (0–10)",
    8  => "How much has pain interfered with your: Walking ability. (0–10)",
    9  => "How much has pain interfered with your: Normal work (outside & housework). (0–10)",
    10 => "How much has pain interfered with your: Relationships with other people. (0–10)",
    11 => "How much has pain interfered with your: Sleep. (0–10)",
    12 => "How much has pain interfered with your: Enjoyment of life. (0–10)",
];
?>

<h4 class="mb-3">Edit Patient Form</h4>

<form method="post" action="">
  <input type="hidden" name="id" value="<?= (int)$id ?>">

  <!-- Patient details on one line -->
  <div class="card mb-4">
    <div class="card-header">Patient Details</div>
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">First name</label>
          <input type="text" class="form-control" name="first_name"
                 value="<?= htmlspecialchars($firstName) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Surname</label>
          <input type="text" class="form-control" name="last_name"
                 value="<?= htmlspecialchars($lastName) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" class="form-control" name="dob" id="dob"
                 value="<?= htmlspecialchars($dobStr) ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Age</label>
          <input type="number" class="form-control" name="age" id="age"
                 value="<?= (int)$ageVal ?>" min="0" max="130" required>
        </div>
      </div>
    </div>
  </div>

  <!-- BPI Questions -->
  <div class="card mb-4">
    <div class="card-header">Brief Pain Inventory (BPI)</div>
    <div class="card-body">
      <div class="row g-3">
        <?php for ($i = 1; $i <= 12; $i++):
            $min = ($i === 1) ? 0 : 0;
            $max = ($i === 1) ? 100 : 10;
        ?>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Q<?= $i ?>. <?= htmlspecialchars($questions[$i]) ?></label>
            <input type="number"
                   class="form-control q-input"
                   name="q<?= $i ?>"
                   id="q<?= $i ?>"
                   min="<?= $min ?>"
                   max="<?= $max ?>"
                   step="1"
                   value="<?= (int)$qVals[$i] ?>"
                   required>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Total Score -->
  <div class="card mb-3">
    <div class="card-header">Total Score</div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Total (Q2–Q12)</label>
          <input type="number"
                 class="form-control fw-bold"
                 name="total_score_display"
                 id="total_score_display"
                 value="<?= (int)$totalScore ?>"
                 readonly>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-secondary" href="/admin/index.php">Cancel</a>
    <button type="submit" class="btn btn-primary">Save changes</button>
  </div>
</form>

<!-- JS: auto-calc age & total score -->
<script>
function calcAgeFromDob(dobStr) {
  if (!dobStr) return '';
  const dob = new Date(dobStr);
  if (isNaN(dob.getTime())) return '';
  const today = new Date();
  let age = today.getFullYear() - dob.getFullYear();
  const m = today.getMonth() - dob.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
  return age >= 0 ? age : '';
}

document.addEventListener('DOMContentLoaded', () => {
  const dobInput = document.getElementById('dob');
  const ageInput = document.getElementById('age');
  const totalInput = document.getElementById('total_score_display');

  function recomputeTotal() {
    let sum = 0;
    for (let i = 2; i <= 12; i++) {
      const el = document.getElementById('q' + i);
      const v = parseInt(el && el.value ? el.value : '0', 10);
      sum += isNaN(v) ? 0 : v;
    }
    totalInput.value = sum;
  }

  // initial total compute
  recomputeTotal();

  // update total on any q-change
  for (let i = 1; i <= 12; i++) {
    const el = document.getElementById('q' + i);
    if (el) el.addEventListener('input', recomputeTotal);
  }

  // auto age from dob
  if (dobInput) {
    dobInput.addEventListener('change', () => {
      const a = calcAgeFromDob(dobInput.value);
      if (a !== '') ageInput.value = a;
    });
  }
});
</script>

<?php require_once __DIR__ . '/../../footer.php'; ?>
