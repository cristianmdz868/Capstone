<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Client</title>
    <link rel="stylesheet" href="style1.css"> <!-- Include your CSS file -->
    <style>
        /* Style for the navbar */
        .navbar {
            overflow: hidden;
            background-color: #333;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a.right {
            float: right;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <!-- Home link on the right corner -->
        <a href="advisor_dashboard.php" class="right">Home</a>
    </div>

    <div class="container">
        <div class="box">
            <h2>Add Client</h2>

            <!-- Error message section -->
            <?php
            if (isset($_GET['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>

            <!-- Add client and car form -->
            <form action="advisor_add_client_action.php" method="POST">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <!-- Car details -->
                <h3>Add Car Details</h3>
                <div class="form-group">
                    <label for="vin">VIN:</label>
                    <input type="text" id="vin" name="vin" required>
                </div>

                <div class="form-group">
                    <label for="model">Model:</label>
                    <input type="text" id="model" name="model" required>
                </div>

                <div class="form-group">
                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required>
                </div>

                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" min="1900" max="2099" step="1" required>
                </div>

                <button type="submit">Add Client & Car</button>
            </form>
        </div>
    </div>
</body>
</html>
