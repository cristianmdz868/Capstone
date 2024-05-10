<?php
    $host="cristian.cikeys.com";
    $user="cristia1_Mendoza";
    $password="RedRover23";
    $database="cristia1_Capstone";
// Establish the database connection
$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
