<?php
    session_start();

    ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);

    include("../Database/connect.php");
    include("Validation.php");

    $error = '';

    // Check the database connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    

        // Process request type
        $requestType = ($_POST['RequestType'] === 'Bug') ? 1 : 2;

        // Process priority
        $priority = $_POST['priority'];
        $priorityValue;
        switch ($priority) {
            case 'top':
                $priorityValue = 3;
                break;
            case 'medium':
                $priorityValue = 2;
                break;
            default:
                $priorityValue = 1;
                break;
        }

        // Get User Id
        $user = $_SESSION['user_id'];
        echo $user;

        // Prepare and execute the SQL statement
        $sql = "SELECT Tenant FROM User WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param("i", $user);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $tenantId = $row["Tenant"];
        
        echo "Tenant: " . $tenant;

        // Get form data
        $Statement = $_POST['Statement'];
        $Solution = $_POST['Solution'];
        $requestedDate = date("Y-m-d");
        $expectedDate = $_POST['ExpectedDate'];
        $expectedDateValue;

        // Process expected date
        switch ($expectedDate) {
            case '1':
                $expectedDateValue = date('Y-m-d', strtotime($requestedDate . ' + 5 days'));
                break;
            case '2':
                $expectedDateValue = date('Y-m-d', strtotime($requestedDate . ' + 15 days'));
                break;
            case '3':
                $expectedDateValue = date('Y-m-d', strtotime($requestedDate . ' + 30 days'));
                break;
            default:
                $expectedDateValue = NULL;
                break;
        }

        // Insert data into the database
        $sql = "INSERT INTO ChangeRequest (User, RequestType, Statement, Suggestion, RequestedDate, Priority, ExpectedDate)
                VALUES (?,?,?,?,?,?)";

        $stmt = $mysqli->prepare($sql);

        // Bind parameters
        $stmt->bind_param("iisssisss", $user['Id'], $requestType, $Statement, $Solution, $requestedDate, $priorityValue, $expectedDate);

        // Execute the query
        if ($stmt->execute()) {
            // Respond with a success message if needed
            $error = 'Change request added successfully';
        } else {
            // Respond with an error message if needed
            $error = 'Error adding change request';
        }
    

?>
