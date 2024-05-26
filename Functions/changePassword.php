<?php
    include("Validation.php");
    include("../Database/connect.php");
    $conn = $mysqli;
    $success='';
    
    if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) 
        && ($_GET["action"]=="reset") && !isset($_POST["action"])){
        $key = $_GET["key"];
        $email = $_GET["email"];
        $curDate = date("Y-m-d H:i:s");
        $error ='';
        $sql = "SELECT * FROM ResetToken WHERE Email = ? AND Token =?";
        $query = $conn->prepare($sql);

        $query->bind_Param('ss', $email,$key);
        $query->execute();
        $result = $query->get_result();
        //var_dump($result);
        if ($result->num_rows <= 0){
            

            $error .= '<h2>Invalid Link</h2>
                <p>The link is invalid/expired. Either you did not copy the correct link
                from the email, or you have already used the key in which case it is 
                deactivated.</p>';

        }else{
            $row = mysqli_fetch_assoc($result);
            $expDate = $row['expDate'];
            if ($expDate < $curDate){
                ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shiftsync-Reset-Password</title>
    <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://elitex.co.za/ProjectC/Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
            border: 2px solid ; /* Orange border */
        }
    </style>
    </head>

<body>
<br/>
<div class="container mt-5">
    <div class="row justify-content-center">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                <img src="https://elitex.co.za/ProjectC/Assets/Logo.png" width="100px" alt="Logo">
                    <h3 class="text-center">Password Reset</h3>
                </div>
                <div class="card-body">
                <form method="post" action="" name="reset">
                <p><?php echo $success;?></p>
                          
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="token" class="form-label">Reset Token</label>
                            <input type="text" class="form-control" id="token" name="token" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <input type="submit" value="Reset Password" class="btn btn-primary"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}else{
        $error .= "<h2>Link Expired</h2>
        <p>The link is expired. You are trying to use the expired link which 
        as valid only 24 hours (1 days after request).<br /><br /></p>";
                    }
            }
        if($error!=""){
        echo "<div class='error'>".$error."</div><br />";
        }else{
            if(isset($_POST["email"]) && isset($_POST["token"]) &&
        ($_POST["newPassword"])){
                $error="";
                $pass1 = $_POST["newPassword"];
                $pass2 = $_POST["confirmPassword"];
                $email = $_POST["email"];
                $key = $_POST['token'];
                $curDate = date("Y-m-d H:i:s");
        $pattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z]).{8,}$/';
        if ($pass1!=$pass2){
                $error.= "<p>Password do not match, both password should be same.<br /><br /></p>";
        }
        if(!preg_match($pattern,$pass1)){
            $error .= "<p>Incorrect password Format</p>";
        }
        
        if($error!=""){
                echo "<div class='error'>".$error."</div><br />";
        }else{

            $hash = password_hash($pass1,PASSWORD_BCRYPT);
            $isActive = 1;
            $Attempts = 0;
            $sql = "UPDATE User SET PasswordHash=?, IsActive = ?, LoginAttempts =? WHERE Email = ?";
            $query = $conn->prepare($sql);
            $query->bind_param('siis', $hash,$isActive,$Attempts,$email);
            //var_dump($query);
            $query->execute();
            $success .= "Successfully Changed Password...";
            
            // $sqlII = "DELETE FROM ResetToken WHERE Email = ? AND Token =?";
            // $queryII = $conn->prepare($sqlII);
    
            // $queryII->bind_Param('ss', $email,$key);
            // //var_dump($queryII);
            // $queryII->execute();

            echo '<script>
                    alert("Successfully Changed Your password...");
                    window.location.href = "../Login.php";</script>';
                }		
            }
        }		
    }
         // isset email key validate end
?>
<!-- Bootstrap JS and Popper.js -->
<script>
    function Cancel(){
        window.location.href = "../Login.php";
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>