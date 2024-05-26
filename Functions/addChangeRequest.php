<?php
    session_start();
    include("../Database/connect.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

?>

<html lang="en">
    
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Shift Sync- Add Change Request</title>
        <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@11/bootstrap-4.css">
        
        <!-- Add Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet"href="https://elitex.co.za/ProjectC/Styles/styles.css">
    
    </head>

    <body>

        <?php
            include '../Layout/header.php';
        ?>

        <div class="rounded  container mx-auto m-5 p-3">
            <h2>Add New Change Request</h2>
            
            <form action="" method="post" id="Form" enctype="multipart/form-data">

                <div class="form-group mx-5 px-5">
                <!-- Change the "name" attribute to "requestType" for both buttons -->
                <!-- Your nav pills section -->
                <ul id="RequestType" name="RequestType" class="nav nav-pills nav-fill justify-content-center gap-2 p-1 small bg-secondary rounded-5 shadow-sm" id="pillNav2" role="tablist" style="--bs-nav-link-color: var(--bs-white); --bs-nav-pills-link-active-color: var(--bs-primary); --bs-nav-pills-link-active-bg: var(--bs-white);">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-5" id="home-tab2" data-bs-toggle="tab" type="button" role="tab" aria-selected="true" name="requestTypeBug" value="Bug" onclick="updateHiddenInput('requestTypeBug', 'Bug')">Bug</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-5" id="profile-tab2" data-bs-toggle="tab" type="button" role="tab" aria-selected="false" name="requestTypeNew" value="New Request" onclick="updateHiddenInput('requestTypeNew', 'New Request')">New Request</button>
                    </li>
                </ul>

                <input type="hidden" name="RequestType" id="hiddenRequestType" value="Bug">

                <!-- Your form and other HTML content -->
                </div>

                <div class="form-group my-3 d-flex justify-content-center">
                    Priority:
                    <label class="radio-inline mx-2">
                        <input type="radio" name="priority" value="low" checked> Low
                    </label>
                    <label class="radio-inline mx-2">
                        <input type="radio" name="priority" value="medium"> Medium
                    </label>
                    <label class="radio-inline mx-2">
                        <input type="radio" name="priority" value="high"> High
                    </label>
                </div>

                <div class="form-group mx-3">
                    <label for="Title" id="Title">Title:</label>
                    <input type="text" class="form-control" id="Title" name="Title" required></input>
                </div>

                <div class="form-group mx-3">
                    <label for="Statement" id="StatementLabel">Statement:</label>
                    <textarea class="form-control" id="Statement" name="Statement" rows="4" required></textarea>
                </div>

                <div class="form-group mx-3">
                    <label for="Solution" id="SolutionLabel">Solution:</label>
                    <textarea class="form-control" id="Solution" name="Solution" rows="4" ></textarea>
                </div>

                <div class="form-group mx-3 ">
                    <label for="ExpectedDate" >Expected Date:</label>
                    <input type="date" class="form-control" id="ExpectedDate" name="ExpectedDate" ></input>
                </div>

                <div class="form-group mx-3">
                    <label for="AffectedProcesses" id="AffectedProcesses">AffectedProcesses:</label>
                    <input type="text" class="form-control" id="AffectedProcesses" name="AffectedProcesses"></input>
                </div>

                <div class="form-group mx-3">
                    <label for="fileUpload">File Upload:</label>
                    <input type="file" class="form-control" id="fileUpload" name="fileUpload">
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-Primary m-3"></input>
                    <button type="button" class="btn btn-Secondary m-3" onclick="Cancel();">Cancel</button>
                </div>

            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // JavaScript function to update the value of the hidden input field for the active nav pill
            function updateHiddenInput(hiddenFieldId, value) {
                // Set the value of the corresponding hidden field based on the active nav pill
                document.getElementById('hiddenRequestType').value = value;
            }
        </script>

        <script>
            function Cancel(){
                window.location.href = "../Admin/ChangeRequests.php";
            }
        </script>

    </body>

</html>

<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        /* ------- Retrieve data from Form -------*/

        $requestType = $_POST['RequestType'];
        $priority = $_POST['priority'];
        $title = $_POST['Title'];
        $statement = $_POST['Statement'];
        $solution = $_POST['Solution'];
        $expectedDate = $_POST['ExpectedDate'];
        $affectedProcesses = $_POST['AffectedProcesses'];
        $title = $_POST['Title'];


        /* ------- Use data from Form -------*/

        $requestTypeValue = ($requestType == 'Bug') ? 1 : 2;

        $priorityValue;
        switch ($priority) {
            case 'medium':
                $priorityValue = 2;
                $labelName = 'Medium';
                $labelColor = 'orange';
                break;
            case 'high':
                $priorityValue = 1;
                $labelName = 'High';
                $labelColor = 'red';
                break;
            default:
                // Handle other cases if needed
                $priorityValue = 3; // Default value
                $labelName = 'Low';
                $labelColor = 'green';
        }

        $requestedDate = date("Y-m-d H:i:s");

        
        /*----------------- File Upload -----------------*/

        if (isset($_FILES["fileUpload"]) && $_FILES["fileUpload"]["error"] == UPLOAD_ERR_OK) {
    
        $targetDirectory = "../Uploads/"; // Make sure this directory exists
        $uploadedFileName = basename($_FILES["fileUpload"]["name"]);
        $targetFilePath = $targetDirectory . $uploadedFileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));


        $allowedExtensions = array("jpg", "jpeg", "png", "gif", "mp4", "avi", "mov");
        if (in_array($fileType, $allowedExtensions)) {
            if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $targetFilePath)) {
                // File uploaded successfully, update the database with the file path
                $filePathInDb = str_replace("../", "", $targetFilePath); // Update this based on your database structure
            } else {
                echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Failed to upload the file.",
                        icon: "error"
                    }).then(function() {
                        window.location.href = "../Admin/ChangeRequests.php";
                    });
                </script>';
                exit; // Exit script if file upload fails
            }
        } else {
            echo '<script>
                Swal.fire({
                    title: "Error",
                    text: "Invalid file type. Allowed types: jpg, jpeg, png, gif, mp4, avi, mov.",
                    icon: "error"
                }).then(function() {
                    window.location.href = "../Admin/ChangeRequests.php";
                });
            </script>';
            exit; // Exit script if the file type is not allowed
        }
    }

        /* ------- Retrieve Tenant id from DB -------*/

        $userId = $_SESSION['user_id'];
        // echo "UserID: ".$userId."<br>";
            
        $sql = "SELECT Tenant, Email FROM User WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($tenantId, $email);

        if ($stmt->fetch()) {
            $_SESSION['tenant_id'] = $tenantId;
            $_SESSION['email'] = $email;

            echo "Tenant ID: ".$tenantId."<br>";
            echo "email: ".$email."<br>";
        } else {
            echo "User with ID ".$userId." not found.<br>";
        }
            
        $stmt->close();


        /* ------- Retrieve APIKey, APIToken and defaultlist id from DB -------*/

        // Prepare and execute the SQL statement
        $sql = "SELECT APIKey, APIToken, DefaultList FROM Tenant WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param("i", $tenantId);
        $stmt->execute();
        $stmt->bind_result($apiKey, $apiToken, $defaultList);

        // Fetch the result
        if ($stmt->fetch()) {
            $_SESSION['APIKey'] = $apiKey;
            $_SESSION['APIToken'] = $apiToken;
            $_SESSION['defaultList'] = $defaultList;

            echo "APIKey: ".$apiKey."<br>";
            echo "APIToken: ".$apiToken."<br>";
            echo "defaultList: ".$defaultList."<br>";

        } else {
            echo "Tenant with ID ".$tenantId." not found.<br>";
        }

        $stmt->close();

        /* ------- Retrieve Board id, trelloList and SubStatus from DB -------*/

        // Prepare and execute the SQL statement
        $sql = "SELECT Board, TrelloList, SubStatus FROM List WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param("i", $defaultList);
        $stmt->execute();
        $stmt->bind_result($boardid, $trelloList, $subStatusId);

        // Fetch the result
        if ($stmt->fetch()) {
            $_SESSION['trelloList'] = $trelloList;
            $_SESSION['board'] = $boardid;
            $_SESSION['subStatus_id'] = $subStatusId;

            echo "trelloList: ".$trelloList."<br>";
            echo "board: ".$boardid."<br>";
            echo "subStatus_id: ".$subStatusId."<br>";
        } else {
            echo "List with ID ".$defaultList." not found.<br>";
        }

        $stmt->close();

        /* ------- Retrieve Status from DB -------*/

        $sql = "SELECT Status FROM SubStatus WHERE Id = ?";
        $stmt = $mysqli->prepare($sql);

        $stmt->bind_param('i', $subStatusId);
        $stmt->execute();
        $stmt->bind_result($statusId);

        if ($stmt->fetch()) {
            $_SESSION['status_Id'] = $statusId;
        } else {
            echo "SubStatus with ID ".$subStatusId." not found.<br>";
        }

        $stmt->close();


        /* ------- Perform POST Request to Trello -------*/
        
        $baseUrl = 'https://api.trello.com/1/cards';
        $cardName = "{$requestType} - {$affectedProcesses} - {$title}";
        
        if (isset($filePathInDb)) {
            $fileURL = $filePathInDb;
            $fileURL = 'https://elitex.co.za/ProjectC/'.$fileURL;
        } else {
             $fileURL = '';
        }
        

        $desc = "**Problem Statement:**\n\n{$statement}\n\n**Proposed Solution:**\n\n{$solution}\n\n{$fileURL}\n\n-- Created by: {$email}";
        $duedate = $expectedDate;

        $url = "$baseUrl?name='$cardName&idList=$trelloList&key=$apiKey&token=$apiToken&desc=$desc&due=$duedate&pos=top";

        $urlII = $baseUrl.'?name='.$cardName.'&idList='.$trelloList.'&key='.$apiKey.'&token='.$apiToken.'&desc='.$desc.'&due='.$duedate.'&pos=top';

        $postData = array(
            'name' => $cardName,
            'idList' => $trelloList,
            'key' => $apiKey,
            'token' => $apiToken,
            'desc' => $desc,
            'due' => $duedate,
            'pos' => 'top'
        );
        
        $urlII = $baseUrl . '?' . http_build_query($postData);

        $curl = curl_init();
                
        curl_setopt_array($curl, array( 
            CURLOPT_URL => $urlII,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo '<br>Request Details: ' . print_r(curl_getinfo($curl), true) . PHP_EOL;
            echo 'Response: ' . $response . PHP_EOL;

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            echo '<br><br>HTTP Status Code: ' . $httpCode . PHP_EOL;

            die('<br><br>Curl error: ' . curl_error($curl));
        } else {
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($httpStatus == 200) {
                
                $cardData = json_decode($response, true);

                if ($cardData !== null && is_array($cardData)) {

                    $_SESSION['cardData'] = array();

                    // Extract values
                    $cardId = $cardData['id'];
                    $shortLink = $cardData['shortLink'];
                    $shortUrl = $cardData['shortUrl'];

                    // Store in session or use as needed
                    $_SESSION['cardData']['id'] = $cardId;
                    $_SESSION['cardData']['shortLink'] = $shortLink;
                    $_SESSION['cardData']['shortUrl'] = $shortUrl;

                    
                    /* ------- Insert Data into DB if request successful -------*/

                    $sql = "INSERT INTO ChangeRequest (User, RequestType, Statement, Suggestion, RequestDate, Priority, Status, Substatus, DateExpected, List, ShortCardURL, TrelloCard, Tenant, AffectedProcesses, FilePath, Title)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
                    $query = $mysqli->prepare($sql);
                    $query->bind_param('iisssiiisississs', $userId, $requestTypeValue, $statement, $solution, $requestedDate, $priorityValue, $statusId, $subStatusId, $expectedDate, $defaultList, $shortUrl, $cardId, $tenantId, $affectedProcesses, $filePathInDb, $title);
                
                    
                    if ($query->execute()) {
                        $requestId = $query->insert_id;

                        $sqlCapture="INSERT INTO ChangeRequestHistory (ChangeRequest, User, RequestType, Statement, Suggestion, ChangedDate, Priority, Status, SubStatus, DateExpected, FilePath, List, AffectedProcesses, Title)
                        SELECT CR.Id, CR.User, CR.RequestType, CR.Statement, CR.Suggestion, NOW(), CR.Priority, CR.Status, CR.SubStatus, CR.DateExpected, CR.FilePath, CR.List, CR.AffectedProcesses, CR.Title
                        FROM ChangeRequest CR
                        WHERE CR.Id = ?";
        
                        $queryInsertHistory = $mysqli->prepare($sqlCapture);
                        $queryInsertHistory->bind_param('i', $requestId);
                        
                        // Execute the query
                        $queryInsertHistory->execute();

                        //Close the prepared statement
                        $queryInsertHistory->close();

                        echo "<script>
                        Swal.fire({
                            title: 'Success!',
                            text: 'Change Request added successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            // Redirect user to the specified page
                            window.location.href = '../Admin/ChangeRequests.php';
                        });
                    </script>";
                    
                    } else {
                        echo '<script>
                        Swal.fire({
                            title: "Error",
                            text: "Could not Add Change Request.",
                            icon: "error"
                        }).then(function() {
                            // Redirect user to the specified page
                            // window.location.href = "../Admin/ChangeRequests.php";
                        });
                    </script>';
                    }

                } else {
                    echo '<script>
                        Swal.fire({
                            title: "Error",
                            text: "Error: Invalid JSON structure or decoding failed.",
                            icon: "error"
                        }).then(function() {
                            // Redirect user to the specified page
                            // window.location.href = "../Admin/ChangeRequests.php";
                        });
                    </script>';
                }
            } else {
                echo '<script>
                        Swal.fire({
                            title: "Error",
                            text: "Error: Error httpStatus !200",
                            icon: "error"
                        }).then(function() {
                            // Redirect user to the specified page
                            // window.location.href = "../Admin/ChangeRequests.php";
                        });
                    </script>';
                echo ".";
            }
        }
        curl_close($curl);

        $labelURL = "https://api.trello.com/1/cards/{$cardId}/labels?key={$apiKey}&token={$apiToken}&name={$labelName}&color={$labelColor}";

        $ch = curl_init($labelURL);

        curl_setopt_array($ch, array( 
            CURLOPT_URL => $labelURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_VERBOSE => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));

        $labelResponse = curl_exec($ch);

        if (curl_errno($ch)) {
            echo '<br>Request Details: ' . print_r(curl_getinfo($ch), true) . PHP_EOL;
            echo 'Response: ' . $labelResponse . PHP_EOL;

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            echo '<br><br>HTTP Status Code: ' . $httpCode . PHP_EOL;

            die('<br><br>Curl error: ' . curl_error($ch));
        } else {
            if ($httpStatus == 200) {

                $labelData = json_decode($labelResponse,true);

                if ($labelData !== null && is_array($labelData)) {

                    $_SESSION['labelData'] = array();

                    // Extract values
                    $labelId = $labelData['id'];
                    $labelName = $labelData['name'];

                } else {
                    echo "<br>invalid labelData JSON structure or decoding failed";
                }

            }else{
                echo "<br>Label not added httpstatus not OK";
            }
        }
        curl_close($ch);

        }

?>