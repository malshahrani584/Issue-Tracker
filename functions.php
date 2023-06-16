<?php
function check_login($conn)
{
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return false;
}

function log_action($conn, $action, $user_id, $issue_id)
{
    $timestamp = date("Y-m-d H:i:s");
    $query = "INSERT INTO logs (action, user_id, issue_id, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siis", $action, $user_id, $issue_id, $timestamp);
    $stmt->execute();
}
?>