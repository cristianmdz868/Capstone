<?php
session_name('advisor_session');
session_start();
include "db_conn.php"; // Include your database connection file

// Check if the search query is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_query'])) {
    // Validate and sanitize search query
    $search_query = validate($_POST['search_query']);

    // Initialize variables to store client information
    $client_id = $client_first_name = $client_last_name = "";

    // Search for the client by first name, last name, or phone number
    $sql_search_client = "SELECT * FROM client WHERE first_name LIKE ? OR last_name LIKE ? OR phone_number LIKE ?";
    $stmt_search_client = mysqli_prepare($conn, $sql_search_client);

    if ($stmt_search_client) {
        // Bind parameters to the prepared statement
        $search_param = "%" . $search_query . "%";
        mysqli_stmt_bind_param($stmt_search_client, 'sss', $search_param, $search_param, $search_param);
        
        // Execute the prepared statement
        mysqli_stmt_execute($stmt_search_client);
        $result_search_client = mysqli_stmt_get_result($stmt_search_client);

        if (mysqli_num_rows($result_search_client) > 0) {
            // Client found, fetch client information
            $row_client = mysqli_fetch_assoc($result_search_client);
            $client_id = $row_client['id'];
            $client_first_name = $row_client['first_name'];
            $client_last_name = $row_client['last_name'];
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt_search_client);
    } else {
        echo "Error preparing search statement: " . mysqli_error($conn);
    }
}

// Close the database connection
mysqli_close($conn);

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Appointment</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3 {
            text-align: center;
            margin: 20px 0;
        }

        /* Form Styles */
        form {
            max-width: 500px; /* Increased form width for better readability */
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px; /* Increased border radius for a softer look */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Slightly increased shadow for depth */
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: calc(100% - 16px); /* Adjusted input width to accommodate border */
            padding: 10px; /* Increased padding for better input aesthetics */
            margin-bottom: 15px; /* Increased margin bottom for better spacing */
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 12px 24px; /* Slightly increased padding for a more clickable button */
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .no-results {
            text-align: center;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .add-client-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .add-client-link:hover {
            color: #0056b3;
        }

        /* Center text within paragraphs */
        p {
            text-align: center;
            margin-bottom: 10px; /* Adjusted margin for spacing */
        }
    </style>
</head>
<body>
    <h1>Advisor Search and Create Appointment</h1>
    <h2>Create Appointment</h2>

    <?php if (!empty($client_id)): ?>
    <h3>Client Found:</h3>
    <p><strong>Name:</strong> <?php echo $client_first_name . ' ' . $client_last_name; ?></p>
    <p><strong>Client ID:</strong> <?php echo $client_id; ?></p>

    <!-- Include appointment creation form -->
    <form action="advisor_create_appointment_action.php" method="POST">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
        <label for="appointment_date">Appointment Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" required>

        <!-- Add other appointment fields as needed -->

        <label for="technician">Choose a Technician:</label>
        <select id="technician" name="technician" required>
            <option value="">Select Technician</option>
            <?php
            // Fetch technicians from the database
            include "db_conn.php";
            $sql_technicians = "SELECT id, first_name, last_name FROM technicians";
            $result_technicians = mysqli_query($conn, $sql_technicians);
            if ($result_technicians && mysqli_num_rows($result_technicians) > 0) {
                while ($row = mysqli_fetch_assoc($result_technicians)) {
                    echo '<option value="' . $row['id'] . '">' . $row['first_name'] . ' ' . $row['last_name'] . '</option>';
                }
            } else {
                echo '<option value="">No Technicians Found</option>';
            }
            ?>
        </select>

        <input type="submit" value="Create Appointment">
    </form>
    
    <?php else: ?>
    <p class="no-results">No client found with the search query. You can add a new client.</p>
    <a href="advisor_add_client.php" class="add-client-link">Add New Client</a>
    <?php endif; ?>
</body>
</html>

