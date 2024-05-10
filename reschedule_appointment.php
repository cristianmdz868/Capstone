<?php
session_name('user_session');
session_start();
include "db_conn.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if appointment ID is set
    if (isset($_POST['appointment_id'])) {
        $appointment_id = $_POST['appointment_id'];

        // Validate the new date and sanitize input
        $new_date = $_POST['new_date']; // You should validate and sanitize this input

        // Update the appointment date in the database
        $sql_update = "UPDATE appointments SET appointment_date = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);

        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "si", $new_date, $appointment_id);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);

            // Redirect to a success page or display a success message
            header("Location: home_customer.php");
            exit();
        } else {
            // Handle the case where the SQL statement preparation fails
            echo "Error updating appointment: " . mysqli_error($conn);
        }
    } else {
        // Handle the case where appointment ID is missing
        header("Location: error_page.php");
        exit();
    }
} else {
    // Handle the case where the form is not submitted via POST
    header("Location: error_page.php");
    exit();
}

mysqli_close($conn);
?>
