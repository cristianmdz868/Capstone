<?php
session_name('user_session');
session_start();
include "db_conn.php"; // Include your database connection file

// Function to validate and sanitize form input data
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in and retrieve user ID from session
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in."); // Stop execution and show error message
}
$client_id = $_SESSION['user_id']; // Use $_SESSION['user_id'] directly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form input data
    $vin = validate($_POST['vin']);
    $model = validate($_POST['model']);
    $color = validate($_POST['color']);
    $year = validate($_POST['year']);

    // Insert car information into the cars table with client_id
    $sql_insert_car = "INSERT INTO cars (client_id, vin, model, color, year) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_car = mysqli_prepare($conn, $sql_insert_car);

    if ($stmt_insert_car) {
        mysqli_stmt_bind_param($stmt_insert_car, 'isssi', $client_id, $vin, $model, $color, $year);
        if (mysqli_stmt_execute($stmt_insert_car)) {
            // Car information inserted successfully, now link it to the client
            $sql_update_client = "UPDATE client SET car_vin = ? WHERE id = ?";
            $stmt_update_client = mysqli_prepare($conn, $sql_update_client);

            if ($stmt_update_client) {
                mysqli_stmt_bind_param($stmt_update_client, 'si', $vin, $client_id);
                if (mysqli_stmt_execute($stmt_update_client)) {
                    // Car linked to client successfully
                    header("Location: home_customer.php"); // Redirect back to home page or wherever needed
                    exit();
                } else {
                    die("Error updating client with car VIN: " . mysqli_stmt_error($stmt_update_client)); // Stop execution and show error message
                }
            } else {
                die("Error preparing update statement: " . mysqli_error($conn)); // Stop execution and show error message
            }
        } else {
            die("Error inserting car information: " . mysqli_stmt_error($stmt_insert_car)); // Stop execution and show error message
        }
    } else {
        die("Error preparing insert statement: " . mysqli_error($conn)); // Stop execution and show error message
    }
}
?>
