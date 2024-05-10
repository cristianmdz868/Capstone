<?php
session_name('advisor_session');
session_start();
include "db_conn.php"; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['advisor_id']) || empty($_SESSION['advisor_id'])) {
    header("Location: Login.php");
    exit();
}

// Retrieve data from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $technician_id = $_POST['technician'];

    // Prepare and execute the SQL statement
    $sql_assign_technician = "UPDATE appointments SET technician_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_assign_technician);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $technician_id, $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt); // Close the statement after execution

        // Redirect to the dashboard after successful assignment
        header("Location: advisor_dashboard.php");
        exit();
    } else {
        echo "Error preparing assignment statement: " . mysqli_error($conn);
    }

    mysqli_close($conn); // Close the database connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Technician</title>
</head>
<body>
    <h1>Assign Technician to Appointment</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="appointment_id">Appointment ID:</label>
        <input type="text" id="appointment_id" name="appointment_id" readonly value="<?php echo $_GET['appointment_id']; ?>">

        <label for="technician">Choose a Technician:</label>
        <select id="technician" name="technician">
            <option value="">Select Technician</option>
            <?php
            // Include your database connection file
            include "db_conn.php";

            // Fetch technicians from the database
            $sql_technicians = "SELECT id, first_name, last_name FROM technicians";
            $result_technicians = mysqli_query($conn, $sql_technicians);

            if ($result_technicians && mysqli_num_rows($result_technicians) > 0) {
                while ($row = mysqli_fetch_assoc($result_technicians)) {
                    echo '<option value="' . $row['id'] . '">' . $row['first_name'] . ' ' . $row['last_name'] . '</option>';
                }
            } else {
                echo '<option value="">No Technicians Found</option>';
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </select>

        <input type="submit" value="Assign Technician">
    </form>
</body>
</html>
