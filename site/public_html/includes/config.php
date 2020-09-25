<?php
$domain = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$options[\PDO::ATTR_PERSISTENT]=true;

$servername = "db";
$username = "root";
$password = "zuc*TPOI4&n6VfLAbSI54p*0";
$dbName = "rgsfurniture_dev";
$rpp = 16;

try {
 $conn = new PDO("mysql:host=$servername;dbname=$dbName", $username, $password);
 // set the PDO error mode to exception
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
 echo "Connection failed: " . $e->getMessage();
}
?>
