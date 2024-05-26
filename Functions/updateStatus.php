<?php

include("../Database/connect.php");

// Check if the required parameters are set
if (isset($_POST['userId']) && isset($_POST['action'])) {
    $userId = $_POST['userId'];
    $action = $_POST['action'];

    // Validate the action (optional, based on your requirements)
    if ($action === 'suspend') {
        // Perform the action to suspend the user (update the database, etc.)
        $sql = "UPDATE User SET IsActive = 0 WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            echo "User suspended successfully!";
        } else {
            echo "Error suspending user: " . $stmt->error;
        }

        $stmt->close();
    } elseif ($action === 'unsuspend') {
        // Perform the action to unsuspend the user (update the database, etc.)
        $sql = "UPDATE User SET IsActive = 1 WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            echo "User unsuspended successfully!";
        } else {
            echo "Error unsuspending user: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid action!";
    }
} else {
    echo "Missing parameters!";
}
?>
