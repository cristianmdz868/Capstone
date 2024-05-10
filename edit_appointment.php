<?php
// Start the session
session_name('user_session');
session_start();

// Include your database connection file
include "db_conn.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: Login.php");
    exit();
}

// Check if appointment_id is set and not empty
if (isset($_POST['appointment_id']) && !empty($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Fetch appointment details
    $sql = "SELECT * FROM appointments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $appointment_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Appointment details found, proceed with editing
            // Here, you can display a form to edit appointment details
            // For example, display the current date and allow the user to select a new date
            echo '<h2>Edit Appointment</h2>';
            echo '<p>Current Date: ' . $row['appointment_date'] . '</p>';
            echo '<form action="reschedule_appointment.php" method="POST">';
            echo '<input type="hidden" name="appointment_id" value="' . $appointment_id . '">';
            echo '<label for="new_date">New Date:</label>';
            echo '<input type="date" id="new_date" name="new_date" required>';
            echo '<button type="submit">Save Changes</button>';
            echo '</form>';
        } else {
            // Handle the case where appointment details are not found
            echo 'Appointment not found.';
        }

        mysqli_stmt_close($stmt);
    } else {
        // Handle the case where preparing the statement failed
        echo 'Error preparing appointment statement: ' . mysqli_error($conn);
    }
} else {
    // Redirect or handle the case where appointment_id is missing
    header("Location: error_page.php");
    exit();
}

mysqli_close($conn);
?>
