<?php
session_name('advisor_session');
session_start();
include "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Check if the connection is valid
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL statement
    $sql = "SELECT id, first_name, last_name FROM advisors WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Check if the statement is prepared successfully
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters to the prepared statement
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Get the result set
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            // Advisor login successful, fetch advisor details
            $row = mysqli_fetch_assoc($result);
            $advisor_id = $row['id'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];

            // Store advisor details in session variables
            $_SESSION['advisor_id'] = $advisor_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;

            // Redirect to advisor dashboard or home page
            header("Location: advisor_dashboard.php");
            exit();
        } else {
            // Invalid login, handle accordingly
            header("Location: Login.php?error=Invalid login credentials");
            exit();
        }
    } else {
        die("Error executing statement: " . mysqli_stmt_error($stmt));
    }
}

function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
