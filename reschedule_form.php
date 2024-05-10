<?php
// Start the session
session_name('user_session');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header("Location: Login.php");
    exit();
}

// Include your database connection file
include "db_conn.php";

// Check if appointment_id is set and not empty
if (isset($_GET['appointment_id']) && !empty($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];
} else {
    // Redirect or handle the case where appointment_id is missing
    header("Location: error_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Appointment</title>
    <link rel="stylesheet" href="customer_page/customer_styles.css">
</head>
<body>
    <h2>Reschedule Appointment</h2>
    <form action="reschedule_appointment.php" method="POST">
        <label for="new_date">New Date:</label>
        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
        <input type="date" id="new_date" name="new_date" required><br>
        <!-- Other appointment form fields go here -->
        <button type="submit">Reschedule Appointment</button>
    </form>
</body>
</html>