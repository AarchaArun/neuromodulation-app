<?php

/**
 * Admin Login Page
 * ----------------
 * Provides a simple username/password login for admin access.
 * Uses environment variables for credentials to avoid hardcoding.
 */

session_start();
require_once __DIR__ . '/../../env.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === getenv('ADMIN_USER') && $password === getenv('ADMIN_PASS')) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!-- header layout for bootstrap -->
<?php require_once __DIR__ . '/../../header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="text-center mb-3">Admin Login</h4>
                <?php if ($message): ?>
                    <div class="alert alert-danger py-2"><?php echo $message; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- footer layout for bootstrap -->
<?php require_once __DIR__ . '/../../footer.php'; ?>
