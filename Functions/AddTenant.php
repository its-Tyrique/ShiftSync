<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
?>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shift Sync- Add New Tenant</title>
    <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet"href="https://elitex.co.za/ProjectC/Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@11/bootstrap-4.css">
    
</head>
<body>

<?php
  include '../Layout/header.php';
?>

<div class="rounded border border-dark container mx-auto m-5 p-3">
    <h2>Add New Tenant</h2>
    <form action="" method="post" id="Form">

        <!-- First Name -->
        <div class="form-group">
            <label for="TenantName">Tenant Name:</label>
            <input type="text" class="form-control" id="TenantName" name="TenantName" aria-required="true">
            <small>error message</small>
        </div>

        <!-- Last Name -->
        <div class="form-group">
            <label for="APIKey">Api Key:</label>
            <input type="text" class="form-control" id="APIKey" name="APIKey" aria-required="true">
            <small>error message</small>
        </div>
        <div class="form-group">
            <label for="APIToken">API Token:</label>
            <input type="text" class="form-control" id="APIToken" name="APIToken" aria-required="true">
            <small>error message</small>
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
<script>
const form = document.getElementById('Form');
const TenantName = document.getElementById("TenantName");
const APIKey = document.getElementById("APIKey");
const APIToken = document.getElementById("APIToken");

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

//Validate Email
function checkEmail(input){
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if(re.test(input.value.trim())){
        showSuccess(input)
      }
      else{
        showError(input,"Invalid E-mail Format")
      } 
}
    function ValidateKey(input){
}
    function validateToken(input){

}
//Check required Function
function fieldName(input){
    return innput.id.charAt(0).ToUppercase() + input.id.slice(1);
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
form.addEventListener('submit',function(e){
    e.preventDefault();
    checkRequired([TenantName,APIToken,APIKey]);
    //checkPassword(password)
    $.ajax({
            type: 'POST',
            url: 'https://elitex.co.za/ProjectC/Functions/TenantSuccess.php',
            data: $('#Form').serialize(), // Serialize form data
            success: function (response) {
                console.log(response);
                // Handle the response if needed
                Swal.fire({
                    title: "Success",
                    text: response.message,
                    icon: "success"
                }).then(function () {
                    window.location.href = 'https://elitex.co.za/ProjectC/Admin/Tenants.php';
                });
                    

            },
            error: function (error) {
                console.log(error);
                // Handle the error
                    Swal.fire({
                        title: "Error",
                        text: error.message,
                        icon: "error"
                    });
                }
        })

});
</script>


<script>
    function Cancel(){
        window.location.href = "https://elitex.co.za/ProjectC/Admin/Tenants.php?Id="<?php echo $UserId?>;
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    require('sweetalert2');
</script>
    </body>
</html>