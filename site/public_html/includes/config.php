<?php

$domain = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

$rpp = 16;

$dsn = "mysql:host=".getenv('DB_HOST').";dbname=".getenv('DB_NAME');
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
} catch (PDOException $e) {
    die("Connection Failed!".$e->getMessage());
}

/*if($conn->connect_error){
  die("Connection Failed!".$conn->connect_error);
}*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



?>
