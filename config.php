<?php
// config.php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'jerro_cbt';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

session_start();
?>