<?php
session_name('Technician_session');
session_start();
include "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    $sql = "SELECT id, first_name, last_name FROM technicians WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        // Technician login successful, fetch technician details
        $row = mysqli_fetch_assoc($result);
        $tech_id = $row['id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        // Store technician details in session variables
        $_SESSION['tech_id'] = $tech_id;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        // Redirect to technician dashboard or home page
        header("Location: technician_dashboard.php");
        exit();
    } else {
        // Invalid login, handle accordingly
        header("Location: Login.php?error=Invalid login credentials");
        exit();
    }
}

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
