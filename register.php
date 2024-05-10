<?php
include "db_conn.php"; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form input data
    $first_name = validate($_POST['first_name']);
    $last_name = validate($_POST['last_name']);
    $phone_number = validate($_POST['phone_number']);
    $email = validate($_POST['email']);
    $password = password_hash(validate($_POST['password']), PASSWORD_DEFAULT); // Hash the password

    // Check if the email is already registered
    $sql_check_email = "SELECT id FROM client WHERE email = ?";
    $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, 's', $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);
    $email_count = mysqli_stmt_num_rows($stmt_check_email);

    if ($email_count > 0) {
        // Email already exists, redirect back to registration form with error
        header("Location: registration_form.html?error=Email already exists");
        exit();
    }

    // Check if the phone number is already registered
    $sql_check_phone = "SELECT id FROM client WHERE phone_number = ?";
    $stmt_check_phone = mysqli_prepare($conn, $sql_check_phone);
    mysqli_stmt_bind_param($stmt_check_phone, 's', $phone_number);
    mysqli_stmt_execute($stmt_check_phone);
    mysqli_stmt_store_result($stmt_check_phone);
    $phone_count = mysqli_stmt_num_rows($stmt_check_phone);

    if ($phone_count > 0) {
        // Phone number already exists, redirect back to registration form with error
        header("Location: registration_form.html?error=Phone number already exists");
        exit();
    }

    // Insert the user data into the database
    $sql_insert_user = "INSERT INTO client (first_name, last_name, phone_number, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt_insert_user, 'sssss', $first_name, $last_name, $phone_number, $email, $password);

    if (mysqli_stmt_execute($stmt_insert_user)) {
        // Registration successful, redirect to the login page with a success message
        header("Location: Login.php?registration=success");
        exit();
    } else {
        // Error inserting user data, redirect back to registration form with error
        header("Location: registration_form.php?error=Registration failed");
        exit();
    }
}

// Function to validate and sanitize form input data
function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
