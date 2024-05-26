<?php

    // Retrieve user information from the session
    $user = $_SESSION['user'];

    // Include the database connection file using __DIR__ for an absolute path
    // include('../Database/connect.php');

    // // Assign the database connection to $conn
    // $conn = $mysqli;

    $imageSrc = !empty($user['AvatarPath']) ? "https://elitex.co.za/ProjectC/" . $user['AvatarPath'] : 'https://elitex.co.za/ProjectC/Assets/TransparentDefault.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shift Sync</title>

    <!-- Favicon -->
    <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="https://elitex.co.za/ProjectC/Styles/styles.css">
    <style>
        .header{
            background-image: linear-gradient(to right,#de6262,#ffb88c );

        }
        .nav-link:hover{
            color:grey!important;
        }
        .navbar{
            border-radius:50px
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body>

<div class="header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <!-- Navigation -->
        <div class="container-fluid">
            <!-- Brand Image and Profile Picture -->
            <a class="navbar-brand" href="https://elitex.co.za/ProjectC/Profile.php">
            <img src="<?php echo $imageSrc; ?>" alt="Profile Picture" width="60" height="60" class="d-inline-block align-text-top rounded-circle">
                <?php if ($user["Role"] == 1): ?>
                    <span class="mt-2">Welcome <?php echo $user["FirstName"];?> (Admin)</span>
                <?php else: ?>
                    
                    <span class="mt-2">Welcome <?php echo $user["FirstName"];?></span>
                <?php endif; ?>
            </a>

            <!-- Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end align-center" id="main-nav">
                <ul class="navbar-nav">
                    <!-- Navigation Links -->
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="https://elitex.co.za/ProjectC/Dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://elitex.co.za/ProjectC/Profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://elitex.co.za/ProjectC/Admin/ChangeRequests.php">Change Requests</a>
                    </li>
                    <?php if ($user["Role"] === 1):?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Overviews
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="https://elitex.co.za/ProjectC/Admin/Users.php">Users</a></li>
                                <li><a class="dropdown-item" href="https://elitex.co.za/ProjectC/Admin/Tenants.php">Tenants</a></li>
                               </ul>
                        </li>
                    <?php endif;?>
                    <li class="nav-item">
                        <a class="nav-link" href="https://elitex.co.za/ProjectC/Logout.php">Log-Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
