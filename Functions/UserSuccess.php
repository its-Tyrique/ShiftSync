<?php
include("../Database/connect.php");
include("../Functions/Validation.php");

// Initialize response array
$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = $mysqli;

    // Getting Info from the Form
    if (
        isset($_POST['firstName']) && isset($_POST['lastName']) &&
        isset($_POST['email']) && isset($_POST['phoneNumber']) &&
        isset($_POST['password'])
    ) {
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $email = htmlspecialchars($_POST['email']);
        $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
        $password = htmlspecialchars($_POST['password']); // Hash the password
        $role = htmlspecialchars($_POST['role']);
        $tenant = intval(htmlspecialchars($_POST['tenant']));
        $RoleId;
        $IsActive = 1;
        
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL statement to insert data into the database
        $sql = "INSERT INTO User (FirstName, LastName, Email, CellNumber, PasswordHash, Role, IsActive, LoginAttempts, Tenant) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $LogAttempts = 0;

        if ($role == 'Admin') {
            $RoleId = 1;
        } elseif ($role == 'BoardAdmin') {
            $RoleId = 2;
        } else {
            $RoleId = 3;
        }
        $stmt->bind_param("sssssiiii", $firstName, $lastName, $email, $phoneNumber, $hash, $RoleId, $IsActive, $LogAttempts, $tenant);
        
        if ($stmt->execute()) {
            // Set success flag and message
            $response['success'] = true;
            $response['message'] = 'User added successfully';
        } else {
            $response['message'] = 'Error executing query: ' . $stmt->error;
        }
    } else {
        $response['message'] = 'Missing required fields';
    }
}

// Convert the response array to JSON and echo it
echo json_encode($response);
?>
