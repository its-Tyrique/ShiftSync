<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include("Database/connect.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /* ------- Retrieve Tenant id from DB -------*/

    $userId = $_SESSION['user_id'];

    $sql = "SELECT Tenant, Email FROM User WHERE Id = ?";
    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($tenantId, $email);

    if ($stmt->fetch()) {
        $_SESSION['tenant_id'] = $tenantId;
        $_SESSION['email'] = $email;
    } else {
        // echo "User with ID ".$userId." not found.<br>";
    }
        
    $stmt->close();


    /* ------- Retrieve APIKey, APIToken and defaultlist id from DB -------*/

    // Prepare and execute the SQL statement
    $sqlApiKey = "SELECT APIKey, APIToken FROM Tenant WHERE Id = ?";
    $stmtAuth = $mysqli->prepare($sqlApiKey);

    $stmtAuth->bind_param("i", $tenantId);
    $stmtAuth->execute();
    $stmtAuth->bind_result($apiKey, $apiToken);

    // Fetch the result
    if ($stmtAuth->fetch()) {
        // $_SESSION['APIKey'] = $apiKey;
        // $_SESSION['APIToken'] = $apiToken;
    } else {
        // echo "Tenant with ID ".$tenantId." not found.<br>";
    }

    $stmtAuth->close();

    // Prepare and execute the SQL statement
    $sql = "
    SELECT
        CR.Id AS ChangeRequestId,
        COALESCE(CR.List <> COALESCE(CRH.List, CR.List), 0) AS ListChanged,
        CR.TrelloCard AS TrelloCard
    FROM
        ChangeRequest CR
    LEFT JOIN ChangeRequestHistory CRH
        ON CR.Id = CRH.ChangeRequest
        AND CRH.ChangedDate = (
            SELECT MAX(ChangedDate)
            FROM ChangeRequestHistory
            WHERE ChangeRequest = CR.Id
        );
    ";

    $stmtCompareCR = $mysqli->prepare($sql);

    if (!$stmtCompareCR) {
    die("Error in prepared statement: " . $mysqli->error);
    }

    // Execute the statement
    $stmtCompareCR->execute();

    // Bind the result variables
    $stmtCompareCR->bind_result($changeRequestId, $listChanged, $trelloCards);
    
    // Declare an array to store Change Request IDs
    $changeRequestIds = array();
    $trelloCardsArray = array();

    // Fetch the results
    while ($stmtCompareCR->fetch()) {

        if ($listChanged === 1){
            // echo "Change Request ID: $changeRequestId, List Changed: $listChanged, Trello Card: $trelloCards<br><br>";
            $changeRequestIds[] = $changeRequestId;
            $trelloCardsArray[] = $trelloCards;
        }
    }
    
    // Close the statement
    $stmtCompareCR->close();

    // Loop through each Change Request ID in the array
    for ($i = 0; $i < count($changeRequestIds); $i++) {

        $currentChangeRequestId = $changeRequestIds[$i];
        $currentTrelloCard = $trelloCardsArray[$i];

        /*------------------ GET TrelloCard Details ------------------*/
        
        // Trello API endpoint to get card details
        $trelloApiUrl = "https://api.trello.com/1/cards/$currentTrelloCard?key=$apiKey&token=$apiToken";

        $curl = curl_init();

        //cURL options
        curl_setopt($curl, CURLOPT_URL, $trelloApiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);

        $response = curl_exec($curl);

        if(curl_errno($curl)){
            echo 'Curl error '.curl_error($curl);
        }else{
            // Decode the JSON response
            $cardData = json_decode($response, true);
            // print_r($cardData);

            
            // Check if the card data is valid
            if ($cardData) {
                // Extract relevant data from the Trello card
                $idBoard = $cardData['idBoard'];
                $idList = $cardData['idList'];
                $title = $cardData['name'];

                // echo '<br><br>Card is on Board: '.$idBoard.'';
                // echo '<br>Card is on List: '.$idList.'<br>';

            } else {
                // echo "<br>Error fetching Trello card data for TrelloCard: $currentTrelloCard<br><br>";
            }
        }

        curl_close($curl);
        
        /*------------------ GET User ID ------------------*/

        // SQL statement
        $sql = "SELECT User FROM ChangeRequestHistory WHERE ChangeRequest = ?";

        // Prepare the statement
        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            die("Error in prepared statement: " . $mysqli->error);
        }

        $stmt->bind_param('i', $currentChangeRequestId);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();

        // echo "User ID: " . $userId.'<br>';

        $stmt->close();


        /*------------------ GET Email Address ------------------*/

        // SQL query
        $sql = "SELECT FirstName, Email FROM User WHERE Id = ?";

        // Prepare the statement
        $stmt = $mysqli->prepare($sql);

        // Bind the parameter
        $stmt->bind_param("i", $userId);

        // Execute the query
        $stmt->execute();

        // Bind the result variable
        $stmt->bind_result($requesterName ,$email);

        // Fetch the result
        $stmt->fetch();

        // Output the email
        // echo "User Email: $email<br>";

        // Close the statement
        $stmt->close();


        SendUpdate($requesterName,$email, $currentTrelloCard,$title);

        /*------------------ INSERT LATEST CR INTO CRH ------------------*/

        // Query1 Move CR -> CRH
        $sqlCapture = "INSERT INTO ChangeRequestHistory (ChangeRequest, User, RequestType, Statement, Suggestion, ChangedDate, Priority, Status, SubStatus, DateExpected, FilePath, List, AffectedProcesses, Title)
        SELECT CR.Id, CR.User, CR.RequestType, CR.Statement, CR.Suggestion, NOW(), CR.Priority, CR.Status, CR.SubStatus, CR.DateExpected, CR.FilePath, CR.List, CR.AffectedProcesses, CR.Title
        FROM ChangeRequest CR
        WHERE CR.Id = ?";

        // Prepare the statement
        $queryInsertHistory = $mysqli->prepare($sqlCapture);

        if (!$queryInsertHistory) {
            die("Error in prepared statement: " . $mysqli->error);
        }

        // Bind the Change Request ID parameter
        $queryInsertHistory->bind_param('i', $currentChangeRequestId);

        // Execute the query
        $queryInsertHistory->execute();

        // Close the prepared statement
        $queryInsertHistory->close();

        // Optionally, you can also print a message after the loop
        // echo "ChangeRequestHistory records added successfully for Change Request ID: $currentChangeRequestId.<br><hr>";
    }

    // Close the database connection
    $mysqli->close();

?>
<?php 
    function SendUpdate($_requesterName ,$_email,$_ticketId,$_title){
        $output='';
            $output.='<p>Please click on the button to reset your password.</p>';
            $output.='<p>-------------------------------------------------------------</p>';
            $output.=' <p style="text-align:center"><a href="https://elitex.co.za/ProjectC/Functions/changePassword.php?key=&email='.$_email.'&action=reset" target="_blank"
                    style="background-color: #0dcaf0;
                        color: white; 
                        padding: 5px 10px;
                        text-decoration: none;
                        border-radius:20px;
                        ">Reset Password</a></p>';
            $output.='<p>Here is your Token Key:<h3>'.'</h3></p>';		
            $output.='<p>-------------------------------------------------------------</p>';
            $output.='<p>Please be sure to copy the Token into your browser.
                        The Token will expire after 1 day for security reason.</p>';
            $output.='<p>If you did not request this forgotten password email, no action 
                        is needed, your password will not be reset. However, you may want to log into 
                        your account and change your security password as someone may have guessed it.</p>';   	
            $output.='<p>Thanks,</p>';
            $output.='<p>Shift Sync Team</p>';

            $hmtl_temp = '
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <title>Shift Sync- Admin</title>
                <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
                <!-- Add Bootstrap CSS -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
                <link rel="stylesheet"href="https://elitex.co.za/ProjectC/Styles/styles.css">
                <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
        
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #ffffff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
        
                h1 {
                    color: #333;
                }
        
                p {
                    color: #555;
                }
        
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
        
                .logo img {
                    max-width: 150px; /* Adjust the size as needed */
                    height: auto;
                }
        
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                }
            </style>
            </head>
            <body>
            <div class="container">
                <div class="logo">
                    <img src="https://elitex.co.za/ProjectC/Assets/Logo.png" width="100">
                </div>
                <h1>Change Request Update</h1>
                <p>Hello '. $_requesterName .',</p>
                <p>We want to inform you that there has been an update on your change request ticket.</p>
                <p><strong>Ticket Details:</strong></p>
                <ul>
                    <li><strong>Change Request: </strong> '.$_title.'</li>
                    <li><strong>Request ID: </strong>'.$_ticketId. '</li>
                    
                    <!-- Add more details as needed -->
                </ul>
                <br>
                <strong>Moved To  A new list </strong>
                <p>You can view the details by logging into your account.</p>
                <p>Thank you for using our support system.</p>
                <p>Best regards,<br>Shit Sync</p>
            </div>
        </body>
        
        </html>
            ';

            $body = $hmtl_temp; 
            $subject = "Request ticket update - Shift Sync";

            $email_to = $_email;
            $fromserver = "ShiftSync@outlook.com"; 

            require 'vendor/autoload.php'; // Adjust the path based on your project structure

            $mail = new PHPMailer(true);

            $mail->IsSMTP();
            $mail->Host = "smtp-mail.outlook.com"; // Enter your host here
            $mail->SMTPAuth = true;
            $mail->Username = "ShiftSync@outlook.com"; // Enter your email here
            $mail->Password = "eliteXprojectC@2024"; //Enter your password here
            $mail->Port = 587;

            $mail->IsHTML(true);

            $mail->From = "ShiftSync@outlook.com"; // Change this to a valid email address

            $mail->FromName = "Shift Sync";
            $mail->Sender = $fromserver; // indicates ReturnPath header
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AddAddress($email_to);
            if(!$mail->Send()){
                echo "Mailer Error: " . $mail->ErrorInfo;
            }else{
               

            }
    }
?>