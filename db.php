<?php
require_once __DIR__ . '/env.php';

//for Reading environment variables
$server = $_ENV['DB_SERVER'];
$database = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'] ?? null;
$pass = $_ENV['DB_PASS'] ?? null;

// for Building connection options safely
$connectionOptions = [
    "Database" => $database,
    "TrustServerCertificate" => true
];

// Only add UID/PWD if user is specified
if (!empty($user)) {
    $connectionOptions["UID"] = $user;
    $connectionOptions["PWD"] = $pass;
}

//for Attempting connection
$dbConnection = sqlsrv_connect($server, $connectionOptions);

if (!$dbConnection) {
    echo "<pre>❌ Connection failed.\n";
    print_r(sqlsrv_errors());
    echo "</pre>";
    die();
}
?>
