<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'sql104.infinityfree.com';
$dbname = 'if0_41422333_neulibrary';
$username = 'if0_41422333';
$password = 'axel200626';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>