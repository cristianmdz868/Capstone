<?php
include "db_conn.php";
session_name('user_session');
session_start(); // Start or resume a session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    $sql = "SELECT * FROM client WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // Passwords match, login successful
            // Store user data in session variables for later use
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];

            // Redirect to home page or dashboard
            header("Location: home_customer.php");
            exit();
        } else {
            // Incorrect password, redirect with error message
            header("Location: Login.php?error=Incorrect password");
            exit();
        }
    } else {
        // User not found, redirect with error message
        header("Location: Login.php?error=User not found");
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
