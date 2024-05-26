<?php
session_start();

include '../Database/connect.php';
$db = $mysqli;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_SESSION['user'];

    // Update the user's AvatarPath to null
    $sql = "UPDATE User SET AvatarPath=NULL WHERE Id = ?";
    $query = $db->prepare($sql);
    $query->bind_param('i', $user["Id"]);
    $query->execute();

    if ($query->error) {
        echo json_encode(['success' => false, 'message' => 'Error updating profile picture']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Profile picture removed successfully']);
    }
} else {
    // Handle invalid request method
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
