<?php
session_start();

$user = $_SESSION['user'];

// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);

include '../Database/connect.php';
$db = $mysqli;

echo $_SERVER;
echo $_FILES;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $profileImageName = $_FILES["image"]["name"];
    $tempFilePath = $_FILES["image"]["tmp_name"];
    echo 'Post method Returned True';
    $firstName = $_POST['FirstName'];
    $cellNumber = $_POST['CellNumber'];

    // For image upload
    $target_dir = "../Assets/Images/";
    $target_file = $target_dir . basename($profileImageName);

    // Move the uploaded image to the target directory
    if (move_uploaded_file($tempFilePath, $target_file)) {
        echo 'Moved File Upload..';
        // Update the database with the new avatar path
        $sql = "UPDATE User SET AvatarPath=?, FirstName = ?, CellNumber =? WHERE Id = ?";
        $query = $db->prepare($sql);
        $query->bind_param('sssi', $target_file,$firstName,$cellNumber, $user["Id"]);
        $query->execute();
        echo 'Updated..';
        if ($query->error) {
            $response = "Error executing SQL query: " . $query->error;
        } else {
            echo'Updated successfully...';
            $response = "Image uploaded and saved in the Database";
            //session_reset();
        }
    } else {
        $response = "Error copying uploaded file. Check directory permissions.";
    }

    echo $response;
} else {
    // Handle invalid request or provide a default response
    echo "Invalid request";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['image'])) {
    echo 'Post method Returned True';
    $firstName = $_POST['FirstName'];
    $cellNumber = $_POST['CellNumber'];

    // For image upload

        echo 'Moved File Upload..';
        // Update the database with the new avatar path
        $sql = "UPDATE User SET , FirstName = ?, CellNumber =? WHERE Id = ?";
        $query = $db->prepare($sql);
        $query->bind_param('ssi', $firstName, $cellNumber, $user["Id"]);
        $query->execute();
        echo 'Updated..';
        if ($query->error) {
            $response = "Error executing SQL query: " . $query->error;
        } else {
            echo'Updated successfully...';
            $response = "Image uploaded and saved in the Database";
            //session_reset();
        }


    echo $response;
} else {
    // Handle invalid request or provide a default response
    echo "Invalid request";
}
?>
