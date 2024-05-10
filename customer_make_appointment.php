<?php
session_name('user_session');
session_start();
include "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['model'], $_POST['advisor'], $_POST['appointment_date'])) {
    $client_id = $_SESSION['user_id'];
    $advisor_id = $_POST['advisor'];
    $vin = $_POST['model'];
    $appointment_date = $_POST['appointment_date'];

    // Validate input data as needed

    // Prepare the SQL statement to insert the appointment
    $sql_create_appointment = "INSERT INTO appointments (client_id, advisor_id, vin, appointment_date) VALUES (?, ?, ?, ?)";
    $stmt_create_appointment = mysqli_prepare($conn, $sql_create_appointment);

    if ($stmt_create_appointment) {
        mysqli_stmt_bind_param($stmt_create_appointment, 'iiis', $client_id, $advisor_id, $car_id, $appointment_date);

        if (mysqli_stmt_execute($stmt_create_appointment)) {
            echo "Appointment created successfully!";
        } else {
            echo "Error creating appointment: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_create_appointment);
    } else {
        echo "Error preparing appointment statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Invalid request!";
}
?>
