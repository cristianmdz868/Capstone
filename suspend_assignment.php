<?php
// Include your database connection file
include "db_conn.php";

// Check if appointment ID is set in the POST data
if (isset($_POST['appointment_id'])) {
    $appointmentId = $_POST['appointment_id'];

    // Update the status to 'Suspended' in the appointments table
    $sql_suspend_assignment = "UPDATE appointments SET status = 'Suspended' WHERE id = ?";
    $stmt_suspend_assignment = mysqli_prepare($conn, $sql_suspend_assignment);

    if ($stmt_suspend_assignment) {
        mysqli_stmt_bind_param($stmt_suspend_assignment, "i", $appointmentId);
        mysqli_stmt_execute($stmt_suspend_assignment);
        mysqli_stmt_close($stmt_suspend_assignment);

        echo "Assignment suspended successfully";
    } else {
        echo "Error suspending assignment: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Invalid data";
}
?>
