<?php
// checks whether admin is logged in - before showing selected form details
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../header.php';
require_once __DIR__ . '/../../db.php';

/**
 * Safely format SQLSRV date/datetime values which may come as DateTime objects or strings.
 * Returns '' if null/empty.
 */
function fmtDate($v, string $pattern = 'Y-m-d'): string {
    if ($v instanceof DateTimeInterface) {
        return $v->format($pattern);
    }
    if (is_string($v) && $v !== '') {
        $ts = strtotime($v);
        return $ts ? date($pattern, $ts) : '';
    }
    return '';
}

// --- load record ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /admin/index.php');
    exit;
}

$sqlGetForm = "{CALL get_PatientFormById(?)}";
$params = [$id];
$res = sqlsrv_query($dbConnection, $sqlGetForm, $params);
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

// pre-format date fields safely
$dobStr        = fmtDate($record['DOB'] ?? null, 'Y-m-d');
$submittedStr  = fmtDate($record['DateSubmitted'] ?? null, 'Y-m-d H:i');

// exact question texts (per spec)
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

<h4 class="mb-3">Patient Form (Read-only)</h4>

<div class="card mb-4">
  <div class="card-body">
    <!-- Patient Details -->
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">First Name</label>
        <input class="form-control" value="<?= htmlspecialchars((string)($record['FirstName'] ?? '')) ?>" disabled>
      </div>
      <div class="col-md-3">
        <label class="form-label">Surname</label>
        <input class="form-control" value="<?= htmlspecialchars((string)($record['LastName'] ?? '')) ?>" disabled>
      </div>
      <div class="col-md-3">
        <label class="form-label">Date of Birth</label>
        <input class="form-control" value="<?= htmlspecialchars($dobStr) ?>" disabled>
      </div>
      <div class="col-md-3">
        <label class="form-label">Age</label>
        <input class="form-control" value="<?= (int)($record['Age'] ?? 0) ?>" disabled>
      </div>
    </div>

    <hr>

    <!-- BPI Questions Q1–Q12 -->
    <div class="row g-3">
      <?php for ($i = 1; $i <= 12; $i++):
            $col = 'Q' . $i;
            $val = isset($record[$col]) ? (int)$record[$col] : 0;
      ?>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Q<?= $i ?>. <?= htmlspecialchars($questions[$i]) ?></label>
          <input class="form-control" value="<?= $val ?>" disabled>
        </div>
      <?php endfor; ?>

      <div class="col-md-3">
        <label class="form-label">Total Score (Q2–Q12)</label>
        <input class="form-control fw-bold" value="<?= (int)($record['TotalScore'] ?? 0) ?>" disabled>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date Submitted</label>
        <input class="form-control" value="<?= htmlspecialchars($submittedStr) ?>" disabled>
      </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2 mt-4">
      <a class="btn btn-secondary" href="/admin/index.php">Back</a>
      <a class="btn btn-primary" href="/admin/edit.php?id=<?= (int)($record['ID'] ?? 0) ?>">Edit</a>

      <form method="post" action="/admin/delete.php" onsubmit="return confirm('Delete this record permanently?');">
        <input type="hidden" name="id" value="<?= (int)$record['ID'] ?>">
        <button type="submit" class="btn btn-danger">Delete</button>
      </form>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../footer.php'; ?>
