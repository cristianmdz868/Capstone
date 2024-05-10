<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="error-container">
        <h1>Error</h1>
        <p>Sorry, an error occurred while processing your request.</p>

        <!-- Optional error details -->
        <?php
        if (isset($_SESSION['error_message'])) {
            echo "<p>Error Message: " . $_SESSION['error_message'] . "</p>";
            unset($_SESSION['error_message']); // Clear the error message after displaying
        }
        ?>

        <p>Return to <a href="index.php">Homepage</a></p>
        <p>Login <a href="login.php">Here</a></p>
        <p>Contact <a href="contact.php">Support</a></p>
    </div>
</body>
</html>
