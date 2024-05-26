<?php
include("../Database/connect.php");
include("Validation.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$error = '';
$response = array();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = $mysqli;
    $error = '';
    
    // Getting Info from the Form
    if (
        isset($_POST['tenantId']) && isset($_POST['APIKey']) && isset($_POST['APIToken'])
    ) {
        
        $tenantId = $_POST['tenantId'];
        $APIKey = $_POST['APIKey'];
        $APIToken = $_POST['APIToken'];
            
        
        $userApiKey = $APIKey; //user input key goes here
        $userApiToken = $APIToken;//user input for token goes here
            
            // GET ALL Boards API Request
            $baseUrl = 'https://api.trello.com/1/members/me/boards?open=true';
            $url = "$baseUrl&key=$userApiKey&token=$userApiToken";
        
            // Initialize cURL session
            $curl = curl_init();
        
            // Set cURL options
                curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: dsc=4869c61849ee470916fcb31d704a0177684f00ea8e408cb17c30e00af4d43000; preAuthProps=s%3A65795a0ebd743d8fc140bac2%3AisEnterpriseAdmin%3Dfalse.ek5U3kyioS2Rk9fHvS9%2BZE%2FoQGvHIgaVcUix%2F8z5B9g'
                ),
            ));
        
            // Execute cURL session and get the result
            $response = curl_exec($curl);
            //$hash = password_hash($password, PASSWORD_BCRYPT);
            // Check for cURL errors
            if (curl_errno($curl)) {
                $error = 'Curl error: ' . curl_error($curl);
            } else {
                $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
                if ($httpStatus == 200) {
                    
                    // Handle the case where the API key and token combination IS correct
                    $sql = 'UPDATE Tenant SET APIKey = ?,APIToken = ?
                            WHERE Id = ?';
                    $stmt = $conn->prepare($sql);
                   
                    $stmt->bind_param("ssi",  $APIKey, $APIToken, $tenantId);

                    $stmt->execute();
                        // Respond with a success message if needed
                        $response['status'] = 'success';
                        $response['message'] = 'Tenant edited successfully';
                        
                    }
                    else {
                        // Respond with an error message if needed
                        $response['status'] = 'error';
                        $response['message'] = 'Error editing Tenant';
                    }
            } 
                // Close cURL session
                curl_close($curl);
       
                header('Content-Type: application/json');
                echo json_encode($response);
                    exit;
        // Prepare and execute the SQL statement to insert data into the database

    }
}
?>