<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0; /* Light grey background */
        }

        .navbar {
            background-color: #007bff; /* Primary color for navbar */
            color: #fff; /* Text color for navbar links */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center; /* Center vertically */
        }

        .navbar h3 {
            margin: 0;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .button-group {
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth background transition */
        }

        button.active {
            background-color: #0056b3; /* Darker shade for active button */
            color: #fff; /* Text color for active button */
        }

        form {
            display: none;
        }

        form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
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
    <!-- Navbar code -->
    <div class="navbar">
        <div class="logo">
            <h3>Cap<span>tone</span></h3>
        </div>
    </div>

    <div class="container">
        <h2>Login</h2>
        <!-- Button group to switch between customer, technician, and advisor login forms -->
        <div class="button-group">
            <button id="customerBtn" onclick="showCustomerForm()" class="active">Customer Login</button>
            <button id="technicianBtn" onclick="showTechnicianForm()">Technician Login</button>
            <button id="advisorBtn" onclick="showAdvisorForm()">Advisor Login</button>
        </div>

        <!-- Customer Login Form -->
        <form id="customerForm" action="customer_login.php" method="POST" class="active">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Customer Login</button>
            <!-- Registration link for customers -->
            <a href="registration_form.php" class="add-client-link">New Customer? Register here</a>
        </form>

        <!-- Technician Login Form -->
        <form id="technicianForm" action="technician_login.php" method="POST">
            <div class="form-group">
                <label for="techEmail">Email:</label>
                <input type="email" id="techEmail" name="email" placeholder="Enter technician email" required>
            </div>
            <div class="form-group">
                <label for="techPassword">Password:</label>
                <input type="password" id="techPassword" name="password" placeholder="Enter technician password" required>
            </div>
            <button type="submit">Technician Login</button>
        </form>

        <!-- Advisor Login Form -->
        <form id="advisorForm" action="advisor_login.php" method="POST">
            <div class="form-group">
                <label for="advisorEmail">Email:</label>
                <input type="email" id="advisorEmail" name="email" placeholder="Enter advisor email" required>
            </div>
            <div class="form-group">
                <label for="advisorPassword">Password:</label>
                <input type="password" id="advisorPassword" name="password" placeholder="Enter advisor password" required>
            </div>
            <button type="submit">Advisor Login</button>
        </form>

        <!-- Script to toggle visibility of login forms and highlight active button -->
        <script>
            function showCustomerForm() {
                document.getElementById('customerForm').classList.add('active');
                document.getElementById('technicianForm').classList.remove('active');
                document.getElementById('advisorForm').classList.remove('active');
                document.getElementById('customerBtn').classList.add('active');
                document.getElementById('technicianBtn').classList.remove('active');
                document.getElementById('advisorBtn').classList.remove('active');
            }

            function showTechnicianForm() {
                document.getElementById('customerForm').classList.remove('active');
                document.getElementById('technicianForm').classList.add('active');
                document.getElementById('advisorForm').classList.remove('active');
                document.getElementById('customerBtn').classList.remove('active');
                document.getElementById('technicianBtn').classList.add('active');
                document.getElementById('advisorBtn').classList.remove('active');
            }

            function showAdvisorForm() {
                document.getElementById('customerForm').classList.remove('active');
                document.getElementById('technicianForm').classList.remove('active');
                document.getElementById('advisorForm').classList.add('active');
                document.getElementById('customerBtn').classList.remove('active');
                document.getElementById('technicianBtn').classList.remove('active');
                document.getElementById('advisorBtn').classList.add('active');
            }
        </script>
    </div>
</body>
</html>
