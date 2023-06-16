<?php
session_start();
require_once("config.php");
require_once("functions.php");

$user = check_login($conn);
if (!$user || $user['role'] !== 'admin') {
    header("Location: login.php");
    die;
}

$error = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($query) === TRUE) {
        header("Location: admin.php");
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin</h1>
    <h2>Add a new user</h2>
    <form method="post" action="admin.php">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Add User</button>
    </form>
    <?php
    if ($error) {
        echo "<p>Error adding user. Please try again.</p>";
    }
    ?>

    <h2>Existing Users</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM users";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row["id"]}</td>";
                echo "<td>{$row["username"]}</td>";
                echo "<td>{$row["role"]}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <h2>Logs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Action</th>
                <th>User</th>
                <th>Issue</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT logs.*, users.username FROM logs INNER JOIN users ON logs.user_id = users.id";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row["id"]}</td>";
                echo "<td>{$row["action"]}</td>";
                echo "<td>{$row["username"]}</td>";
                echo "<td>{$row["issue_id"]}</td>";
                echo "<td>{$row["timestamp"]}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>