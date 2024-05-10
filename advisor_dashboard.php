<?php
session_name('advisor_session');
session_start();
include "db_conn.php"; // Include your database connection file

// Redirect to login page if session variables are missing
if (empty($_SESSION['advisor_id']) || empty($_SESSION['first_name']) || empty($_SESSION['last_name'])) {
    header("Location: Login.php");
    exit();
}

// Access advisor details from session variables
$advisor_id = $_SESSION['advisor_id'];
$first_name = htmlspecialchars($_SESSION['first_name']);
$last_name = htmlspecialchars($_SESSION['last_name']);

// Initialize appointments array
$appointments = [];

// Fetch appointments for the advisor from the database
$currentDate = date('Y-m-d'); // Get current date

$sql_appointments = "SELECT a.*, c.first_name AS client_first_name, c.last_name AS client_last_name, t.first_name AS technician_first_name, t.last_name AS technician_last_name
                    FROM appointments a
                    LEFT JOIN client c ON a.client_id = c.id
                    LEFT JOIN technicians t ON a.technician_id = t.id
                    WHERE a.advisor_id = ? AND DATE(a.appointment_date) >= ?";
$stmt_appointments = mysqli_prepare($conn, $sql_appointments);

if ($stmt_appointments) {
    mysqli_stmt_bind_param($stmt_appointments, 'is', $advisor_id, $currentDate);
    mysqli_stmt_execute($stmt_appointments);
    $result_appointments = mysqli_stmt_get_result($stmt_appointments);

    if (!$result_appointments) {
        echo "Error fetching appointments: " . mysqli_error($conn);
    } else {
        while ($row_appointment = mysqli_fetch_assoc($result_appointments)) {
            // Store each appointment with client information in the array
            $appointments[] = $row_appointment;
        }
    }

    mysqli_stmt_close($stmt_appointments);
} else {
    echo "Error preparing appointment statement: " . mysqli_error($conn);
}

// Fetch appointments for today for the progress section
$appointments_today = [];
$sql_appointments_today = "SELECT a.*, c.first_name AS client_first_name, c.last_name AS client_last_name, t.first_name AS technician_first_name, t.last_name AS technician_last_name
                    FROM appointments a
                    LEFT JOIN client c ON a.client_id = c.id
                    LEFT JOIN technicians t ON a.technician_id = t.id
                    WHERE a.advisor_id = ?
                    AND DATE(a.appointment_date) = CURDATE()"; // Fetch appointments for today

$stmt_appointments_today = mysqli_prepare($conn, $sql_appointments_today);

if ($stmt_appointments_today) {
    mysqli_stmt_bind_param($stmt_appointments_today, 'i', $advisor_id);
    mysqli_stmt_execute($stmt_appointments_today);
    $result_appointments_today = mysqli_stmt_get_result($stmt_appointments_today);

    if (!$result_appointments_today) {
        echo "Error fetching today's appointments: " . mysqli_error($conn);
    } else {
        while ($row_appointment_today = mysqli_fetch_assoc($result_appointments_today)) {
            // Store each appointment for today in the array
            $appointments_today[] = $row_appointment_today;
        }
    }

    mysqli_stmt_close($stmt_appointments_today);
} else {
    echo "Error preparing today's appointment statement: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Dashboard</title>
    <style>
        /* Existing styles */

        /* Style for the navbar */
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

        /* Style for the assign button */
        .assign-btn {
            margin-top: auto; /* Push button to the bottom */
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .assign-btn:hover {
            background-color: #45a049;
        }

        /* Style for the assignment box */
        .assignment-box {
            background-color: #f0f0f0;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer; /* Change cursor to pointer for better UX */
        }

        .assignment-box h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        /* Adjustments for appointments list and items */
        .appointments-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px; /* Adjust spacing between appointment items */
            padding: 0;
            list-style: none;
        }

        .appointment-item {
            flex: 0 0 calc(33.33% - 10px); /* Adjust item width and spacing */
            max-width: calc(33.33% - 10px); /* Adjust max-width to allow wrapping */
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .appointment-info {
            padding: 10px;
        }

        .appointment-info h3 {
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .appointment-info p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }

        .assignment-box-small {
            background-color: #f0f0f0;
            padding: 5px;
            border-radius: 3px;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 14px;
            line-height: 1.5;
            margin-top: auto; /* Push to the bottom */
        }

        /* Additional styles */

        /* Style for the assign button */
        .assign-btn {
            margin-top: auto; /* Push button to the bottom */
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .assign-btn:hover {
            background-color: #45a049;
        }

        /* Style for the assignment box */
        .assignment-box {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer; /* Change cursor to pointer for better UX */
        }

        .assignment-box h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }

        .assignment-box-small {
            background-color: #f0f0f0;
            padding: 5px;
            border-radius: 3px;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 14px;
            line-height: 1.5;
            margin-top: auto; /* Push to the bottom */
        }
         .progress-container {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .progress-info {
            margin-bottom: 10px;
        }

        .progress-bar {
            height: 20px;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 4px;
            position: relative;
        }

        .progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: <?php echo isset($_SESSION['progress']) ? $_SESSION['progress'] . '%' : '0%'; ?>;
            background-color: <?php echo $color ?? '#333'; ?>;
            border-radius: 4px;
            text-align: center;
            color: #fff;
            line-height: 20px;
        }

        /* Styles for Progress Section */
        .progress-boxes {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .progress-box {
            width: 200px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #fff;
        }

        /* Define colors for different progress levels */
        .progress-green { background-color: #4CAF50; }
        .progress-orange { background-color: #FFA500; }
        .progress-blue { background-color: #3498db; }
        .progress-purple { background-color: #9b59b6; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="#/Appointmnets">Appointments</a>
        <a href="advisor_search_and_create_appointment.php">Profile</a>
        <a href="#/Progress">Progression</a>
        <a href="delays.php">Delays</a>
        <a href="Logout.php" class="logout">Logout</a>
    </div>
    
    <!-- Display advisor's information -->
    <h1>Welcome, <?php echo isset($first_name) ? $first_name . ' ' . $last_name : 'Advisor Name'; ?>!</h1>
<!-- Display appointments -->
<h2>Appointments</h2>
<?php if (!empty($appointments)) : ?>
    <ul class="appointments-list">
        <?php foreach ($appointments as $appointment) : ?>
            <li class="appointment-item">
                <div class="appointment-info">
                    <?php if (isset($appointment['client_first_name'])) : ?>
                        <h3><?php echo $appointment['client_first_name'] . ' ' . $appointment['client_last_name']; ?></h3>
                    <?php else : ?>
                        <h3>Client Name Unknown</h3>
                    <?php endif; ?>
                    <p><strong>Appointment ID:</strong> <?php echo $appointment['id']; ?></p>
                    <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                    
                    <!-- Display assigned technician or "Unassigned" -->
                    <?php if (isset($appointment['technician_first_name'])) : ?>
                        <p><strong>Technician:</strong> <?php echo $appointment['technician_first_name'] . ' ' . $appointment['technician_last_name']; ?></p>
                    <?php else : ?>
                        <p><strong>Technician:</strong> Unassigned</p>
                    <?php endif; ?>
                    <!-- Continue with other appointment details -->

                    <!-- Assignment Box as a clickable element -->
                    <div class="assignment-box-small" onclick="assignTechnicianForm(<?php echo $appointment['id']; ?>)">
                        <h3>Assign</h3>
                        <p>ID: <?php echo $appointment['id']; ?></p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>No appointments found for this advisor.</p>
<?php endif; ?>
<div class="progress-container">
<!-- Progress section -->
<section id="progress">
    <h2>Progress</h2>
    <!-- Check if appointments today exist -->
    <?php if (!empty($appointments_today)) : ?>
        <div class="progress-boxes">
            <?php foreach ($appointments_today as $appointment) : ?>
                <!-- Calculate progress level color -->
                <?php
                $progress = $appointment['progress'] ?? 0;
                $color = 'gray'; // Default color
                $status = $appointment['status']; // Get appointment status
                if ($status === 'Suspended') {
                    $color = 'red'; // Set color to red for suspended appointments
                } elseif ($progress >= 75) {
                    $color = 'green';
                } elseif ($progress >= 50) {
                    $color = 'orange';
                } elseif ($progress >= 25) {
                    $color = 'blue';
                } elseif ($progress > 0) {
                    $color = 'purple';
                }
                ?>
                <div class="progress-box" style="background-color: <?php echo $color; ?>;">
                    <h3>Appointment ID: <?php echo $appointment['id']; ?></h3>
                    <p><strong>Client:</strong> <?php echo $appointment['client_first_name'] . ' ' . $appointment['client_last_name']; ?></p>
                    <p><strong>Technician:</strong> <?php echo $appointment['technician_first_name'] . ' ' . $appointment['technician_last_name']; ?></p>
                    <p><strong>Progress:</strong> <?php echo $progress; ?>%</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>No appointments today.</p>
    <?php endif; ?>
</section>

<script>
    function assignTechnicianForm(appointmentId) {
        // Redirect to assign_technician_form.php with appointment ID
        window.location.href = `assign_technician_form.php?appointment_id=${appointmentId}`;
        // Alternatively, you can use AJAX to load the form content asynchronously
    }
</script>
</body>
</html>
