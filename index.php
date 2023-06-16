<?php
session_start();
require_once("config.php");
require_once("functions.php");

$user = check_login($conn);
if (!$user) {
    header("Location: login.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        #add-issue-form {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Issue Tracker</h1>
    
    <button id="show-add-issue-form-button">Add a new issue</button>

    <form id="add-issue-form" method="post">
        <h2>Add a new issue</h2>
        <label for="service_id">Service:</label>
        <select name="service_id" id="service_id" required>
        <?php
        $services_query = "SELECT * FROM services";
        $services_result = $conn->query($services_query);
        while ($service_row = $services_result->fetch_assoc()) {
            echo "<option value='{$service_row["id"]}'>{$service_row["name"]}</option>";
        }
        ?>
        </select>
        <label for="subject">Issue Subject:</label>
        <input type="text" name="subject" id="subject" required>
        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>
        <label for="tickets_number">Tickets Number:</label>
        <input type="text" name="tickets_number" id="tickets_number" required>
        <label for="pm_number">PM Number:</label>
        <input type="text" name="pm_number" id="pm_number" required>
        <label for="email_subject">Email Subject:</label>
        <input type="text" name="email_subject" id="email_subject" required>
        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="1">Pending</option>
            <option value="2">Done</option>
        </select>
        <button type="submit">Add Issue</button>
    </form>

    <table id="issue-table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Service</th>
                <th>Issue Subject</th>
                <th>Description</th>
                <th>Tickets Number</th>
                <th>PM Number</th>
                <th>Email Subject</th>
                <th>Create Date</th>
                <th>User Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT issues.*, users.username, services.name as service_name FROM issues INNER JOIN users ON issues.user_id = users.id INNER JOIN services ON issues.service_id = services.id";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<tr" . ($row["status"] === "Pending" ? ' class="pending"' : '') . " data-issue-id='{$row["id"]}'>";
                echo "<td>{$row["id"]}</td>";
                echo "<td>{$row["service_name"]}</td>";
                echo "<td class='editable-cell' data-field='subject'>{$row["subject"]}</td>";
                echo "<td class='editable-cell' data-field='description'>{$row["description"]}</td>";
                echo "<td class='editable-cell' data-field='tickets_number'>{$row["tickets_number"]}</td>";
                echo "<td class='editable-cell' data-field='pm_number'>{$row["pm_number"]}</td>";
                echo "<td class='editable-cell' data-field='email_subject'>{$row["email_subject"]}</td>";
                echo "<td>" . (new DateTime($row["create_date"]))->format('Y-m-d H:i:s') . "</td>";
                echo "<td>{$row["username"]}</td>";
                echo "<td class='editable-cell' data-field='status'>" . ($row["status"] === "Pending" ? 'Pending' : 'Done') . "</td>";
                echo "<td><button class='update-button'>Update</button></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <button id="export-button">Export to Excel</button>
    <script src="scripts.js"></script>
    <script>
        document.getElementById("show-add-issue-form-button").addEventListener("click", function() {
            var form = document.getElementById("add-issue-form");
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        });
    </script>
</body>
</html>