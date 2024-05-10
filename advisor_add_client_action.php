<?php
session_name('advisor_session');
session_start();
include "db_conn.php"; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $first_name = validate($_POST['first_name']);
    $last_name = validate($_POST['last_name']);
    $phone_number = validate($_POST['phone_number']);
    $email = validate($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $vin = validate($_POST['vin']);
    $model = validate($_POST['model']);
    $color = validate($_POST['color']);
    $year = validate($_POST['year']);

    // Check if the client with the provided email already exists
    $sql_check_client = "SELECT id FROM client WHERE email = ?";
    $stmt_check_client = mysqli_prepare($conn, $sql_check_client);
    mysqli_stmt_bind_param($stmt_check_client, 's', $email);
    mysqli_stmt_execute($stmt_check_client);
    mysqli_stmt_store_result($stmt_check_client);

    if (mysqli_stmt_num_rows($stmt_check_client) > 0) {
        // Client with the provided email already exists, redirect with error
        header("Location: advisor_add_client.php?error=Client with this email already exists.");
        exit();
    }

    // Insert client data into the client table
    $sql_insert_client = "INSERT INTO client (first_name, last_name, phone_number, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_client = mysqli_prepare($conn, $sql_insert_client);
    mysqli_stmt_bind_param($stmt_insert_client, 'sssss', $first_name, $last_name, $phone_number, $email, $password);

    if (mysqli_stmt_execute($stmt_insert_client)) {
        // Client added successfully, now insert car details
        $client_id = mysqli_insert_id($conn); // Get the ID of the newly inserted client

        $sql_insert_car = "INSERT INTO cars (client_id, vin, model, color, year) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_car = mysqli_prepare($conn, $sql_insert_car);
        mysqli_stmt_bind_param($stmt_insert_car, 'isssi', $client_id, $vin, $model, $color, $year);

        if (mysqli_stmt_execute($stmt_insert_car)) {
            // Car details added successfully
            header("Location: advisor_dashboard.php"); // Redirect to success page
            exit();
        } else {
            // Error adding car details
            header("Location: advisor_add_client.php?error=Error adding car details.");
            exit();
        }
    } else {
        // Error adding client
        header("Location: advisor_add_client.php?error=Error adding client.");
        exit();
    }

    mysqli_stmt_close($stmt_check_client);
    mysqli_stmt_close($stmt_insert_client);
    mysqli_stmt_close($stmt_insert_car);
    mysqli_close($conn);
} else {
    // Redirect if accessed without POST method
    header("Location: advisor_add_client.php");
    exit();
}

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
