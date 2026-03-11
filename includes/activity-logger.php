<?php
function logActivity($conn, $user_id, $behavior){
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, behavior, log_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $behavior);
    $stmt->execute();
}
?>