<?php
session_name('advisor_session');
session_start();
include "db_conn.php"; // Include your database connection file

// Check if the user is logged in and advisor ID is set in session
if (!isset($_SESSION['advisor_id']) || empty($_SESSION['advisor_id'])) {
    header("Location: Login.php");
    exit();
}

// Retrieve advisor's ID from session
$advisor_id = $_SESSION['advisor_id'];

// Check if the form is submitted for appointment creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['client_id']) && isset($_POST['appointment_date']) && isset($_POST['technician'])) {
    // Validate and sanitize form inputs
    $client_id = validate($_POST['client_id']);
    $appointment_date = validate($_POST['appointment_date']);
    $technician_id = validate($_POST['technician']);

    // Create the appointment with advisor's ID included
    $sql_create_appointment = "INSERT INTO appointments (client_id, advisor_id, appointment_date, technician_id) VALUES (?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?)";
    $stmt_create_appointment = mysqli_prepare($conn, $sql_create_appointment);

    if ($stmt_create_appointment) {
        mysqli_stmt_bind_param($stmt_create_appointment, 'iisi', $client_id, $advisor_id, $appointment_date, $technician_id);
        mysqli_stmt_execute($stmt_create_appointment);

        // Check if the appointment was successfully created
        if (mysqli_stmt_affected_rows($stmt_create_appointment) > 0) {
            // Redirect to advisor dashboard or any other page
            header("Location: advisor_dashboard.php");
            exit();
        } else {
            echo "Error creating appointment: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_create_appointment);
    } else {
        echo "Error preparing appointment statement: " . mysqli_error($conn);
    }
}

mysqli_close($conn);

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
