<?php
function countUsers($conn){
$q=mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
$r=mysqli_fetch_assoc($q);
return $r['total'];
}
function countChickens($conn){
$q=mysqli_query($conn,"SELECT COUNT(*) AS total FROM chickens");
$r=mysqli_fetch_assoc($q);
return $r['total'];
}
function countLogs($conn){
$q=mysqli_query($conn,"SELECT COUNT(*) AS total FROM user_logs");
$r=mysqli_fetch_assoc($q);
return $r['total'];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Optional: log activity
function logActivity($conn, $user_id, $action) {
    $stmt = $conn->prepare("INSERT INTO user_logs (user_id, behavior, log_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}
?>