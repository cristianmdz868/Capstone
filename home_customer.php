<?php
session_name('user_session');
session_start();
include "db_conn.php"; // Include your database connection file

if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['user_email'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    // Perform other actions based on user data

    // Fetch all cars linked to the current client based on client_id
    $sql_cars = "SELECT model FROM cars WHERE client_id = ?";
    $stmt_cars = mysqli_prepare($conn, $sql_cars);
    mysqli_stmt_bind_param($stmt_cars, 'i', $user_id);
    mysqli_stmt_execute($stmt_cars);
    $result_cars = mysqli_stmt_get_result($stmt_cars);

    // Initialize an array to store all models
    $car_models = [];

    while ($row_car = mysqli_fetch_assoc($result_cars)) {
        // Store each car model in the array
        $car_models[] = $row_car['model'];
    }

    // Update session with fetched car models
    $_SESSION['car_models'] = $car_models;

    // Fetch available advisors
    $sql_advisors = "SELECT id, first_name, last_name FROM advisors";
    $result_advisors = mysqli_query($conn, $sql_advisors);

    // Initialize an array to store all advisors
    $advisors = [];

    while ($row_advisor = mysqli_fetch_assoc($result_advisors)) {
        // Store each advisor in the array
        $advisors[] = $row_advisor;
    }

    // Update session with fetched advisors
    $_SESSION['advisors'] = $advisors;

    // Fetch upcoming appointments with active status for the user
    $sql_appointments = "SELECT appointments.*, advisors.first_name AS advisor_first_name, advisors.last_name AS advisor_last_name FROM appointments JOIN advisors ON appointments.advisor_id = advisors.id WHERE client_id = ? AND status = 'active' AND appointment_date >= CURDATE() ORDER BY appointment_date ASC";
    $stmt_appointments = mysqli_prepare($conn, $sql_appointments);
    mysqli_stmt_bind_param($stmt_appointments, 'i', $user_id);
    mysqli_stmt_execute($stmt_appointments);
    $result_appointments = mysqli_stmt_get_result($stmt_appointments);

    // Initialize an array to store upcoming appointments with advisor names
    $upcoming_appointments = [];

    while ($row_appointment = mysqli_fetch_assoc($result_appointments)) {
        // Store each upcoming appointment with advisor names in the array
        $upcoming_appointments[] = $row_appointment;
    }

    // Check if the form is submitted to create an appointment
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['model'], $_POST['advisor'], $_POST['appointment_date'])) {
        $client_id = $_SESSION['user_id'];
        $advisor_id = $_POST['advisor'];
        $car_id = $_POST['model']; // Assuming 'model' in the form corresponds to 'car_id' in the database
        $appointment_date = $_POST['appointment_date'];

        // Prepare the SQL statement to insert the appointment
        $sql_create_appointment = "INSERT INTO appointments (client_id, advisor_id, car_id, appointment_date) VALUES (?, ?, ?, ?)";
        $stmt_create_appointment = mysqli_prepare($conn, $sql_create_appointment);

        if ($stmt_create_appointment) {
            mysqli_stmt_bind_param($stmt_create_appointment, 'iiis', $client_id, $advisor_id, $car_id, $appointment_date);

            if (mysqli_stmt_execute($stmt_create_appointment)) {
                // Redirect to a different page after successful appointment creation
                header("Location: appointment_success.php");
                exit();
            } else {
                echo "Error creating appointment: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt_create_appointment);
        } else {
            echo "Error preparing appointment statement: " . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Your Account</title>
    <link rel="stylesheet" href="customer_page/customer_styles.css">
</head>
<body>
    <div class="navbar">
        <a href="customer_make_appointment.php">Make Appointment</a>
        <a href="Logout.php">Logout</a> <!-- Modified Logout link -->
    </div>
    <h2>Welcome to Your Account, <?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></h2>
    <div class="container">
        <h2>Make an Appointment</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="model">Select Car Model:</label>
            <select id="model" name="model" required>
                <option value="" disabled selected>Select Model</option>
                <?php
                // Display dropdown options for car models
                foreach ($_SESSION['car_models'] as $car_model) {
                    echo '<option value="' . $car_model . '">' . $car_model . '</option>';
                }
                ?>
            </select><br>

            <label for="advisor">Select Advisor:</label>
            <select id="advisor" name="advisor" required>
                <option value="" disabled selected>Select Advisor</option>
                <?php
                // Display dropdown options for advisors
                foreach ($_SESSION['advisors'] as $advisor) {
                    echo '<option value="' . $advisor['id'] . '">' . $advisor['first_name'] . ' ' . $advisor['last_name'] . '</option>';
                }
                ?>
            </select><br>

            <label for="appointment_date">Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required><br>
            <!-- Other appointment form fields go here -->
            <button type="submit">Make Appointment</button>
        </form>

        <!-- Link to add a new car -->
        <a href="add_car_form.html">Add Another Car</a>

        <h2>Upcoming Appointments</h2>
        <?php if (!empty($upcoming_appointments)) : ?>
            <ul>
                <?php foreach ($upcoming_appointments as $appointment) : ?>
                    <li>
                        <?php echo $appointment['appointment_date']; ?> - Advisor: <?php echo $appointment['advisor_first_name'] . ' ' . $appointment['advisor_last_name']; ?>
                        <!-- Form to submit appointment ID to edit_appointment.php -->
                        <form action="edit_appointment.php" method="POST">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <button type="submit">Edit Appointment</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No upcoming appointments.</p>
        <?php endif; ?>
    </div>
</body>
</html>
