<?php
$host = "localhost";
$port = 3306;
$db   = "classTest2";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



echo "Connected successfully!";
?>
<!-- php ini -->
<!-- extension=zip -->
<!-- remove ; then istall composure -->