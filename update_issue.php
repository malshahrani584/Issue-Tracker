<?php
session_start();
require_once("config.php");
require_once("functions.php");

$user = check_login($conn);
if (!$user) {
    header("Location: login.php");
    die;
}

// Read JSON data from the request body
$json_str = file_get_contents('php://input');
$post_data = json_decode($json_str, true);

$issue_id = $post_data['id'];
$subject = $post_data['subject'];
$description = $post_data['description'];
$tickets_number = $post_data['tickets_number'];
$pm_number = $post_data['pm_number'];
$email_subject = $post_data['email_subject'];
$status = $post_data['status'];

$stmt = $conn->prepare("UPDATE issues SET subject = ?, description = ?, tickets_number = ?, pm_number = ?, email_subject = ?, status = ? WHERE id = ?");
$stmt->bind_param("ssssssi", $subject, $description, $tickets_number, $pm_number, $email_subject, $status, $issue_id);

if ($stmt->execute()) {
    http_response_code(200); // OK
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Issue updated successfully.']);
} else {
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to update issue.']);
}

$stmt->close();
$conn->close();
?>