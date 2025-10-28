<?php

/**
 * Admin Logout Page
 * -----------------
 * Ends the current admin session and redirects the user
 * back to the login page. Ensures all session data is cleared
 * to prevent unauthorized access after logout.
 */

session_start();
session_destroy();
header("Location: login.php");
exit;
