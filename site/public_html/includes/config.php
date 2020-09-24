<?php
$domain = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$options[\PDO::ATTR_PERSISTENT]=true;

$servername = "localhost:3306";
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbName = getenv('DB_NAME');
$rpp = 16;

try {
 $conn = new PDO("mysql:host=$servername;dbname=$dbName", $username, $password);
 // set the PDO error mode to exception
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 echo "Connected successfully";
} catch(PDOException $e) {
 echo "Connection failed: " . $e->getMessage();
}
?>
