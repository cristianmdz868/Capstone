<?php
session_start();
include "db_conn.php"; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $appointment_id = isset($_POST['appointment_id']) ? $_POST['appointment_id'] : '';
    $technician_id = isset($_POST['technician']) ? $_POST['technician'] : '';

    // Check if appointment ID and technician ID are not empty
    if (!empty($appointment_id) && !empty($technician_id)) {
        // Get the technician's name from the database
        $sql_get_technician = "SELECT first_name, last_name FROM technicians WHERE id = ?";
        $stmt_get_technician = mysqli_prepare($conn, $sql_get_technician);

        if ($stmt_get_technician) {
            mysqli_stmt_bind_param($stmt_get_technician, 'i', $technician_id);
            mysqli_stmt_execute($stmt_get_technician);
            $result_technician = mysqli_stmt_get_result($stmt_get_technician);

            if ($row_technician = mysqli_fetch_assoc($result_technician)) {
                $technician_name = $row_technician['first_name'] . ' ' . $row_technician['last_name'];
                // Perform the assignment
                // For example, update the appointments table with the technician ID and name
                $sql_assign_technician = "UPDATE appointments SET technician_id = ?, technician_name = ? WHERE id = ?";
                $stmt_assign_technician = mysqli_prepare($conn, $sql_assign_technician);

                if ($stmt_assign_technician) {
                    mysqli_stmt_bind_param($stmt_assign_technician, 'isi', $technician_id, $technician_name, $appointment_id);
                    if (mysqli_stmt_execute($stmt_assign_technician)) {
                        // Assignment successful
                        $_SESSION['success_message'] = "Technician $technician_name assigned successfully.";
                        header("Location: home_customer.php"); // Redirect to home_client.php
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Error assigning technician: " . mysqli_error($conn);
                    }
                } else {
                    $_SESSION['error_message'] = "Error preparing assignment statement: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_assign_technician);
            } else {
                $_SESSION['error_message'] = "Technician not found.";
            }
        } else {
            $_SESSION['error_message'] = "Error preparing query: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_get_technician);
    } else {
        $_SESSION['error_message'] = "Please select a technician.";
    }

    mysqli_close($conn);
}

// Redirect back to the form page if there was an error
header("Location: make_appointment.php");
exit();
?>
