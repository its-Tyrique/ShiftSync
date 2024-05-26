<?php
    session_start();
    include("../Database/connect.php");
    include("../Functions/Validation.php");
    $conn = $mysqli;
    $error = '';
    ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$sqlII = "SELECT * FROM Role";
$queryII = $conn->prepare($sqlII);
$queryII->execute();
$resultII = $queryII->get_result();

$sqlIII = "SELECT * FROM Tenant";
$queryIII = $conn->prepare($sqlIII);
$queryIII->execute();
$resultIII = $queryIII->get_result();
?>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shift Sync- Add New User</title>
    <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet"href="../Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@11/bootstrap-4.css">
    
</head>
<body>

<?php
  include '../Layout/header.php';
?>

<div class="rounded  container mx-auto m-5 p-3">
    <h2>User Registration</h2>
    <form action="" method="post" id="Form">

<?php echo $error;?>
    <!-- First Name -->
    <div class="form-group">
        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name">
        <small>error message</small>
    </div>

    <!-- Last Name -->
    <div class="form-group">
        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name">
        <small>error message</small>
    </div>

    <!-- Email -->
    <div class="form-group">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email">
        <small>error message</small>
    </div>

    <!-- Phone Number -->
    <div class="form-group">
        <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" pattern="[0-9]{10}" placeholder="Phone Number">
        <small>error message</small>
    </div>

    <!-- Password -->
    <div class="form-group">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
        <small>error message</small>
    </div>
    <div class="form-group">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
        <small>error message</small>
    </div>

    <!-- Role -->
    <!-- Dynamically Add The Roles-->
    <div class="form-group">
        <select class="form-select form-control" id="role" name="role" required>
        <option>Select User Role</option>
            <?php while ($rowII = $resultII->fetch_assoc()) {
                echo '<option value='.$rowII["RoleName"].'>'.$rowII["RoleName"].'</option>';
            }?>
        </select>
    </div>

    <div class="form-group">
        <select class="form-select form-control" id="tenant" name="tenant" required>
            <option>Select Tenant</option>
            <?php while ($rowIII = $resultIII->fetch_assoc()) {
                echo '<option value='.$rowIII["Id"].'>'.$rowIII["TenantName"].'</option>';
            }?>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-Primary m-3">Add</button>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-Secondary m-3" onclick="Cancel();">Cancel</button>
    </div>
</form>

</div>
<!-- Bootstrap JS and Popper.js -->
<?php include '../Layout/footer.php';?>
<!--Javascript For Form Validation-->

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    require('sweetalert2');
</script>
<script>
const form = document.getElementById('Form');
const firstname = document.getElementById("firstName");
const lastname = document.getElementById("lastName");
const email = document.getElementById('email');
const password = document.getElementById('password');
const password_confirm = document.getElementById('confirm_password');
const phone_number = document.getElementById('phoneNumber');


//Show error Function
function showError(input,message){
    const formGroup = input.parentElement;
    formGroup.className = "form-group error";
    const small = formGroup.querySelector('small');
    small.innerText =message;
}

//Show Success Functon
function showSuccess(input){

formGroup = input.parentElement;
formGroup.className = "form-group success";
}

function checkPasswordsMatch(input1, input2){
    if(input1.value !== input2.value){
        showError(input2," Passwords dont matach")
        return false;
    }
    else{
        return true;
    }
}
function checkEmailMatch(input){
    
}
//Validate Email
function checkEmail(input){
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if(re.test(input.value.trim())){
        showSuccess(input);
        return true;
      }
      else{
        showError(input,"Invalid E-mail Format");
        return false;
      } 
}
//Validate Password
function checkPassword(input){
    let re = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
    if(re.test(input.value)){
       // console.log("Password passed")
        showSuccess(input);
        return true;
    }else{
        showError(input, " Passowrd Must meet the following requirements"
             + "\nminimum 8 characters in length"
            +
            "\nAt-least one uppercase English letter"
            +
            "\nAt least one lowercase English letter"
            +
           + "\nAt least one digit"
            +"\nAt least one special character English letter.");
            return false;
    }
}
//Check required Function
function fieldName(input){
    return input.id.charAt(0).ToUppercase() + input.id.slice(1);
}
function checkRequired(inputArr){
    inputArr.forEach(function(input){
        if(input.value.trim() === ''){
            showError(input,"Required")
        }
        else{
            showSuccess(input)
        }
    });
}

//Event Listners
form.addEventListener('submit', function (e) {
    e.preventDefault();
    checkRequired([firstname, lastname, email, password, password_confirm, phone_number]);
    if (checkPassword(password) &&
        checkEmail(email) &&
        checkPasswordsMatch(password, password_confirm)) {
        $.ajax({
            type: 'POST',
            url: '../Functions/UserSuccess.php',
            data: $('#Form').serialize(), // Serialize form data
            success: function (response) {
                // Parse the JSON response
                var responseData = JSON.parse(response);

                // Check if the operation was successful
                if (responseData.success) {
                    Swal.fire({
                        title: "Success",
                        text: "User Successfully Added...",
                        icon: "success"
                    }).then(function () {
                        // Redirect after the user clicks OK
                        window.location.href = 'Users.php';
                    });
                } else {
                    // Display an error message
                    Swal.fire({
                        title: "Error",
                        text: responseData.message,
                        icon: "error"
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                        title: "Error",
                        text: error,
                        icon: "error"
                    });
            }
        });
    }
});

</script>


<script>
    function Cancel(){
        window.location.href = "Users.php";
    }

    
</script>

  <!-- Bootstrap JS -->

   
</body>
</html>