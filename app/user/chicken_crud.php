<?php
session_start();
require "../../config/config.php";
require "../../config/functions.php";

if (!isLoggedIn() || $_SESSION['role'] !== 'user') {
    exit("Access denied");
}

$action = $_REQUEST['action'] ?? '';

switch($action) {

    // List all chickens
    case 'list':
        $q = mysqli_query($conn, "SELECT * FROM chickens");
        echo '<table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tag Number</th>
            <th>Age</th>
            <th>Actions</th>
        </tr>';
        while($row = mysqli_fetch_assoc($q)) {
            echo '<tr>
                <td>'.$row['chicken_id'].'</td>
                <td>'.$row['tag_number'].'</td>
                <td>'.$row['age'].'</td>
                <td>
                    <button onclick="editChicken('.$row['chicken_id'].')">Edit</button>
                    <button onclick="deleteChicken('.$row['chicken_id'].')">Delete</button>
                </td>
            </tr>';
        }
        echo '</table>';
        break;

    // Add new chicken
    case 'add':
        $tag = $_POST['tag_number'];
        $age = $_POST['age'];
        $stmt = $conn->prepare("INSERT INTO chickens (tag_number, age) VALUES (?, ?)");
        $stmt->bind_param("si", $tag, $age);
        $stmt->execute();
        break;

    // Delete chicken
    case 'delete':
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM chickens WHERE chicken_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        break;

    // Edit chicken
    case 'edit':
        $id = $_POST['id'];
        $tag = $_POST['tag_number'];
        $age = $_POST['age'];
        $stmt = $conn->prepare("UPDATE chickens SET tag_number=?, age=? WHERE chicken_id=?");
        $stmt->bind_param("sii", $tag, $age, $id);
        $stmt->execute();
        break;
}