<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Search and Create Appointment</title>
    <style>
        /* Navbar styles */
        .navbar {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-return {
            font-size: 18px;
        }

        /* Form styles */
        h1 {
            text-align: center;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">Capstone</div>
        <div class="navbar-return">
            <a href="advisor_dashboard.php" style="color: #fff; text-decoration: none;">Return to Dashboard</a>
        </div>
    </div>
    <h1>Advisor Search and Create Appointment</h1>

    <form action="advisor_search_and_create_appointment_action.php" method="POST">
        <label for="search_query">Search by First Name, Last Name, or Phone Number:</label>
        <input type="text" id="search_query" name="search_query" required>

        <input type="submit" value="Search">
    </form>
</body>
</html>
