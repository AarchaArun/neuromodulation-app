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

/**
 * Safely format SQLSRV date/datetime values which may come as DateTime objects or strings.
 */
function formatDateTime($value, string $pattern = 'Y-m-d H:i'): string
{
    if ($value instanceof DateTimeInterface) {
        return $value->format($pattern);
    }
    if (is_string($value) && $value !== '') {
        $ts = strtotime($value);
        return $ts ? date($pattern, $ts) : '';
    }
    return '';
}

function formatDateOnly($value): string
{
    return formatDateTime($value, 'Y-m-d');
}

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

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="filterInput" class="form-label mb-1">Filter Records</label>
            <input type="text" id="filterInput" class="form-control" placeholder="Search by name, age, DOB, score...">
        </div>
    </div>

    <table id="formsTable" class="table table-bordered table-striped table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th class="sortable" data-type="date"> Date of Submission
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>

                <th class="sortable" data-type="string"> First Name
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>

                <th class="sortable" data-type="string"> Surname
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>

                <th class="sortable" data-type="number"> Age
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>

                <th class="sortable" data-type="date"> Date of Birth
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>

                <th class="sortable" data-type="number"> Total Score
                <span class="sort-icons">
                    <span class="sort-up">▲</span>
                    <span class="sort-down">▼</span>
                </span>
                </th>
            </tr>
    </thead>

        <tbody>
        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
            <tr class="table-row" style="cursor:pointer" data-id="<?= (int)$row['ID'] ?>">
                <!-- CHANGED: safe formatting for DateSubmitted -->
                <td><?= htmlspecialchars(formatDateTime($row['DateSubmitted'] ?? null, 'Y-m-d H:i')) ?></td>

                <td><?= htmlspecialchars($row['FirstName']) ?></td>
                <td><?= htmlspecialchars($row['LastName']) ?></td>
                <td><?= (int)$row['Age'] ?></td>

                <!-- CHANGED: safe formatting for DOB -->
                <td><?= htmlspecialchars(formatDateOnly($row['DOB'] ?? null)) ?></td>

                <td><?= (int)$row['TotalScore'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const table = document.getElementById('formsTable');
  if (!table) return;

  const tbody = table.querySelector('tbody');
  const filterInput = document.getElementById('filterInput');

  // 1) Row click: go to read-only view
  document.querySelectorAll('.table-row').forEach(function (row) {
    row.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      if (id) {
        window.location.href = '/admin/view.php?id=' + encodeURIComponent(id);
      }
    });
  });

  // 2) Filtering
  if (filterInput) {
    filterInput.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      const rows = tbody.querySelectorAll('tr');

      rows.forEach(function (row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }

  // 3) Sorting
  document.querySelectorAll('th.sortable').forEach((th, index) => {

    const type = th.dataset.type;

    // ASC click
    th.querySelector('.sort-up').addEventListener('click', function (e) {
      e.stopPropagation();
      sortTable(index, type, 1);
    });

    // DESC click
    th.querySelector('.sort-down').addEventListener('click', function (e) {
      e.stopPropagation();
      sortTable(index, type, -1);
    });
  });

  function sortTable(colIndex, type, dir) {
    const rows = Array.from(tbody.querySelectorAll('tr'))
      .filter(row => row.style.display !== 'none');

    rows.sort((a, b) => {
      let aText = a.children[colIndex].textContent.trim();
      let bText = b.children[colIndex].textContent.trim();

      if (type === 'number') {
        aText = parseFloat(aText) || 0;
        bText = parseFloat(bText) || 0;
      } else if (type === 'date') {
        aText = Date.parse(aText) || 0;
        bText = Date.parse(bText) || 0;
      } else {
        aText = aText.toLowerCase();
        bText = bText.toLowerCase();
      }

      if (aText < bText) return -1 * dir;
      if (aText > bText) return 1 * dir;
      return 0;
    });

    rows.forEach(row => tbody.appendChild(row));
  }
});
</script>

<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
