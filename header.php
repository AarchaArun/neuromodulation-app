<!-- 
  Header Include
  --------------
  Opens the main HTML structure and Bootstrap container.
  Contains the site header and navigation layout shared across pages.
-->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuromodulation App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        /* Force consistent navbar look across all pages */
        .navbar-custom {
            background-color: #d6d6d6;   /* light grey tone */
            border-bottom: 1px solid #bcbcbc;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }
        .navbar-custom .nav-link {
            color: #212529 !important;
            font-weight: 500;
        }
        .navbar-custom .nav-link:hover {
            color: #000 !important;
            text-decoration: underline;
        }
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom  shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-dark" href="/form.php">Neuromodulation</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                     <a class="nav-link" href="/form.php">Form</a>
                </li>
                <li class="nav-item">
                      <a class="nav-link" href="/admin/index.php">Admin</a>
                </li>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>

<!-- Add top spacing so content isn’t hidden behind fixed navbar -->
<div class="container mt-3">


