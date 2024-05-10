<?php
session_name('Technician_session');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['tech_id']) || empty($_SESSION['tech_id'])) {
    header("Location: Login.php");
    exit();
}

// Include your database connection file
include "db_conn.php";

// Get today's date in the format matching your database date format
$today_date = date('Y-m-d');

// Fetch appointments for the technician with today's date and client/car details
$sql_appointments = "SELECT appointments.*, 
                            client.first_name AS client_first_name, 
                            client.last_name AS client_last_name, 
                            advisors.first_name AS advisor_first_name, 
                            advisors.last_name AS advisor_last_name, 
                            GROUP_CONCAT(cars.model ORDER BY cars.year) AS car_models,
                            GROUP_CONCAT(cars.year ORDER BY cars.year) AS car_years
                    FROM appointments
                    JOIN client ON appointments.client_id = client.id
                    JOIN advisors ON appointments.advisor_id = advisors.id
                    LEFT JOIN (
                        SELECT client_id, GROUP_CONCAT(model ORDER BY year) AS model, GROUP_CONCAT(year ORDER BY year) AS year
                        FROM cars
                        GROUP BY client_id
                    ) AS cars ON client.id = cars.client_id
                    WHERE appointments.technician_id = ? AND appointments.appointment_date = ?
                    GROUP BY appointments.id";

$stmt_appointments = mysqli_prepare($conn, $sql_appointments);

if ($stmt_appointments) {
    mysqli_stmt_bind_param($stmt_appointments, "is", $_SESSION['tech_id'], $today_date);
    mysqli_stmt_execute($stmt_appointments);
    $result_appointments = mysqli_stmt_get_result($stmt_appointments);

    // Fetch upcoming appointments for the technician excluding today's date and client/car details
    $sql_upcoming_appointments = "SELECT appointments.*, 
                                    client.first_name AS client_first_name, 
                                    client.last_name AS client_last_name, 
                                    advisors.first_name AS advisor_first_name, 
                                    advisors.last_name AS advisor_last_name, 
                                    GROUP_CONCAT(cars.model ORDER BY cars.year) AS car_models,
                                    GROUP_CONCAT(cars.year ORDER BY cars.year) AS car_years
                                FROM appointments
                                JOIN client ON appointments.client_id = client.id
                                JOIN advisors ON appointments.advisor_id = advisors.id
                                LEFT JOIN (
                                    SELECT client_id, GROUP_CONCAT(model ORDER BY year) AS model, GROUP_CONCAT(year ORDER BY year) AS year
                                    FROM cars
                                    GROUP BY client_id
                                ) AS cars ON client.id = cars.client_id
                                WHERE appointments.technician_id = ? AND appointments.appointment_date > ?
                                GROUP BY appointments.id";

    $stmt_upcoming_appointments = mysqli_prepare($conn, $sql_upcoming_appointments);

    if ($stmt_upcoming_appointments) {
        mysqli_stmt_bind_param($stmt_upcoming_appointments, "is", $_SESSION['tech_id'], $today_date);
        mysqli_stmt_execute($stmt_upcoming_appointments);
        $result_upcoming_appointments = mysqli_stmt_get_result($stmt_upcoming_appointments);

        // Display technician's information
        $first_name = $_SESSION['first_name'] ?? 'Unknown';
        $last_name = $_SESSION['last_name'] ?? 'Unknown';
        $tech_id = $_SESSION['tech_id'] ?? 'Unknown';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar a.logout {
            float: right;
        }

        /* Container for appointment boxes */
        .appointments-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: left;
            margin-top: 20px;
            
        }

        /* Styles for individual appointment boxes */
        .appointment-item {
            width: calc(25% - 40px); /* Quarter size with margin and padding */
            margin: 10px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .appointment-item:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .appointment-info {
            margin-bottom: 10px;
        }

        .progress-bar-container {
            display: flex;
            align-items: left;
            margin-top: 10px;
        }

        .progress-bar {
            flex-grow: 1;
            height: 20px;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            position: relative;
            border-radius: 4px;
        }

        .progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            color: #fff;
            text-align: center;
            line-height: 20px;
            width: <?php echo isset($_SESSION['progress']) ? $_SESSION['progress'] . '%' : '0%'; ?>;
            background-color: <?php echo $color ?? '#333'; ?>;
            border-radius: 4px;
        }

        .progress-buttons button {
            margin-right: 10px;
            padding: 8px 16px;
            border: double;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        .progress-buttons button:hover {
            background-color: #0056b3;
        }

        /* Define colors based on progress */
        .progress-bar-25 .progress {
            background-color: purple;
        }

        .progress-bar-50 .progress {
            background-color: blue;
        }

        .progress-bar-75 .progress {
            background-color: orange;
        }

        .progress-bar-100 .progress {
            background-color: green;
        }

        .progress-bar-suspended .progress {
            background-color: red;
        }
    </style>
</head>
<body>
        <!-- Navbar -->
    <div class="navbar">
        <a href="#/Appointmnets">Appointments</a>
        <a href="Logout.php" class="logout">Logout</a>
    </div>
    <!-- Display technician's information -->
    <h1>Welcome, <?php echo "$first_name $last_name"; ?>!</h1>
    <p>Your Technician ID: <?php echo $tech_id; ?></p>

    <!-- Display appointments -->
    <?php if (mysqli_num_rows($result_appointments) > 0) : ?>
        <h2 id="appointments">Today's Appointments:</h2>
        <div class="appointments-container">
            <?php while ($row = mysqli_fetch_assoc($result_appointments)) : ?>
                <div class="appointment-item">
                    <div class="appointment-info">
                        <h3><?php echo $row['client_first_name'] . ' ' . $row['client_last_name']; ?></h3>
                        <p><strong>Appointment ID:</strong> <?php echo $row['id']; ?></p>
                        <p><strong>Advisor:</strong> <?php echo $row['advisor_first_name'] . ' ' . $row['advisor_last_name']; ?></p>
                        <p><strong>Cars:</strong> <?php echo $row['car_models'] . ' (' . $row['car_years'] . ')'; ?></p>
                        <p><strong>Appointment Date:</strong> <?php echo $row['appointment_date']; ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst($row['status']); ?></p>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar <?php echo $row['status'] === 'Suspended' ? 'progress-bar-suspended' : 'progress-bar-' . $row['progress']; ?>">
                            <div class="progress" style="width: <?php echo $row['progress'] ?? '0'; ?>%;">
                                <?php echo $row['progress'] ?? '0'; ?>%
                            </div>
                        </div>
                    </div>
                    <div class="progress-buttons">
                        <button onclick="updateProgress(<?php echo $row['id']; ?>, 25)">25%</button>
                        <button onclick="updateProgress(<?php echo $row['id']; ?>, 50)">50%</button>
                        <button onclick="updateProgress(<?php echo $row['id']; ?>, 75)">75%</button>
                        <button onclick="updateProgress(<?php echo $row['id']; ?>, 100)">Done</button>
                        <button onclick="suspendAssignment(<?php echo $row['id']; ?>)">Delay</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p>No appointments found for today.</p>
    <?php endif; ?>

    <!-- Display upcoming appointments -->
    <?php if (mysqli_num_rows($result_upcoming_appointments) > 0) : ?>
        <h2 id="upcoming-appointments">Upcoming Appointments:</h2>
        <div class="appointments-container">
            <?php while ($row = mysqli_fetch_assoc($result_upcoming_appointments)) : ?>
                <div class="appointment-item">
                    <div class="appointment-info">
                        <h3><?php echo $row['client_first_name'] . ' ' . $row['client_last_name']; ?></h3>
                        <p><strong>Appointment ID:</strong> <?php echo $row['id']; ?></p>
                        <p><strong>Advisor:</strong> <?php echo $row['advisor_first_name'] . ' ' . $row['advisor_last_name']; ?></p>
                        <p><strong>Cars:</strong> <?php echo $row['car_models'] . ' (' . $row['car_years'] . ')'; ?></p>
                        <p><strong>Appointment Date:</strong> <?php echo $row['appointment_date']; ?></p>
                        <!-- No status bar and buttons for upcoming appointments -->
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p>No upcoming appointments found.</p>
    <?php endif; ?>

    <!-- Your JavaScript functions for progress and assignment suspension -->
    <script>
        function updateProgress(appointmentId, progress) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_progress.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        window.location.reload(); // Refresh the page after updating progress
                    } else {
                        console.log(xhr.responseText); // Log the response for debugging
                        // Handle any error scenario here
                    }
                }
            };
            xhr.send('appointment_id=' + appointmentId + '&progress=' + progress);
        }

        function suspendAssignment(appointmentId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'suspend_assignment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('Assignment suspended.');
                    window.location.reload(); // Refresh the page after suspending assignment
                }
            };
            xhr.send('appointment_id=' + appointmentId);
        }
    </script>
</body>
</html>
