<?php
session_name('Technician_session');
session_start();

// Include your database connection file
include "db_conn.php";

// Check if the appointment ID and progress are set in the POST data
if (isset($_POST['appointment_id'], $_POST['progress'])) {
    $appointment_id = $_POST['appointment_id'];
    $progress = $_POST['progress'];

    // Prepare and execute the update SQL query
    $sql_update_progress = "UPDATE appointments SET progress = ? WHERE id = ?";
    $stmt_update_progress = mysqli_prepare($conn, $sql_update_progress);
    
    if ($stmt_update_progress) {
        mysqli_stmt_bind_param($stmt_update_progress, "ii", $progress, $appointment_id);
        mysqli_stmt_execute($stmt_update_progress);

        // Check if any rows were affected by the update
        if (mysqli_stmt_affected_rows($stmt_update_progress) > 0) {
            echo "Debug: Progress updated successfully!";
        } else {
            echo "Debug: No rows updated!";
        }

        mysqli_stmt_close($stmt_update_progress);
    } else {
        echo "Debug: Error preparing progress statement: " . mysqli_error($conn);
    }
    
    // Close your database connection
    mysqli_close($conn);
} else {
    echo "Debug: Missing appointment ID or progress value in POST data!";
}
?>
