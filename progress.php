<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Bar</title>
    <style>
        .progress-bar {
            width: 100%;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            position: relative;
        }

        .progress {
            width: <?php echo isset($_SESSION['progress']) ? $_SESSION['progress'] . '%' : '0%'; ?>;
            height: 30px;
            background-color: #4CAF50;
            text-align: center;
            line-height: 30px;
            color: white;
            position: absolute;
        }

        button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="progress-bar">
        <div class="progress"><?php echo isset($_SESSION['progress']) ? $_SESSION['progress'] . '%' : '0%'; ?></div>
    </div>
    <button onclick="updateProgress(25)">25%</button>
    <button onclick="updateProgress(50)">50%</button>
    <button onclick="updateProgress(75)">75%</button>
    <button onclick="updateProgress(100)">Done</button>
    <button onclick="suspendAssignment()">Delay</button>

    <script>
        function updateProgress(progress) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_progress.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.reload(); // Refresh the page after updating progress
                }
            };
            xhr.send('progress=' + progress);
        }

        function suspendAssignment() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'suspend_assignment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert('Assignment suspended.');
                }
            };
            xhr.send(); // No data needed for suspend
        }
    </script>
</body>
</html>
