<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
</head>
<body>
    <h1>My Appointments</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assume $appointments is an array containing appointment details
            foreach ($appointments as $appointment) {
                echo "<tr>";
                echo "<td>{$appointment['date']}</td>";
                echo "<td>{$appointment['time']}</td>";
                echo "<td>{$appointment['status']}</td>";
                echo "<td>";
                echo "<form action='cancel_appointment.php' method='POST'>";
                echo "<input type='hidden' name='appointment_id' value='{$appointment['id']}'>";
                echo "<button type='submit' name='action' value='cancel'>Cancel</button>";
                echo "</form>";
                echo "<form action='reschedule_appointment.php' method='POST'>";
                echo "<input type='hidden' name='appointment_id' value='{$appointment['id']}'>";
                echo "<button type='submit' name='action' value='reschedule'>Reschedule</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
