<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Technician</title>
</head>
<body>
    <h1>Assign Technician</h1>

    <form action="update_appointment_technician.php" method="POST">
        <label for="appointment_id">Appointment ID:</label>
        <input type="text" id="appointment_id" name="appointment_id" readonly value="<?php echo $appointment_id; ?>">

        <label for="new_technician_id">Choose a Technician:</label>
        <select id="new_technician_id" name="new_technician_id">
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

        <input type="submit" value="Update Technician">
    </form>
</body>
</html>
