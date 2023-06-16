<?php
session_start();
require_once("config.php");
require_once("functions.php");
$user = check_login($conn);
if (!$user) {
    header("Location: login.php");
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = $_POST["service_id"];
    $subject = $_POST["subject"];
    $description = $_POST["description"];
    $tickets_number = $_POST["tickets_number"];
    $pm_number = $_POST["pm_number"];
    $email_subject = $_POST["email_subject"];
    $create_date = date("Y-m-d H:i:s");
    $user_id = $user["id"];
    $status = $_POST["status"];

    $sql = "INSERT INTO issues (service_id, subject, description, tickets_number, pm_number, email_subject, create_date, user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssi", $service_id, $subject, $description, $tickets_number, $pm_number, $email_subject, $create_date, $user_id, $status);

    if ($stmt->execute()) {
        $issue_id = $stmt->insert_id;

        $sql = "SELECT issues.*, users.username, services.name as service_name FROM issues INNER JOIN users ON issues.user_id = users.id INNER JOIN services ON issues.service_id = services.id WHERE issues.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $issue_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue = $result->fetch_assoc();

        // Convert status to string
        $issue["status"] = $issue["status"] == 1 ? "Pending" : "Done";

        header("Content-Type: application/json");
        echo json_encode($issue);
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error: " . $stmt->error));
    }

    $stmt->close();
}