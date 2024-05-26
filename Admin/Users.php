<?php
    session_start();

        include("../Database/connect.php");
        $db = $mysqli;
        // Number of records per page
        $recordsPerPage = 5;

        // Determine current page
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

        // Calculate the starting record for the current page
        $startFrom = ($currentPage - 1) * $recordsPerPage;

        // Initialize filter and sort variables
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'lastname_asc';

        // Define the SQL query based on filter and sort options
        $sql = "SELECT User.*, Role.RoleName, Tenant.TenantName FROM User
                INNER JOIN Role ON User.Role = Role.Id
                LEFT JOIN Tenant ON User.Tenant = Tenant.Id";

        $search = isset($_GET['search']) ? $_GET['search'] : '';

            if (!empty($search)) {
                $sql .= " WHERE User.FirstName LIKE ?";
            }
        $query = $db->prepare($sql);


        // Apply filter
        if ($filter !== 'all') {
            $sql .= ' WHERE Role.RoleName = ?';
        }

// Apply sorting
        switch ($sort) {
            case 'lastname_asc':
                $sql .= ' ORDER BY User.LastName ASC';
                break;
            case 'lastname_desc':
                $sql .= ' ORDER BY User.LastName DESC';
                break;
            // Add more sorting options as needed
        }

// Add LIMIT clause for pagination
$sql .= ' LIMIT ?, ?';

$query = $db->prepare($sql);

        if ($filter !== 'all' && $search =='') {//Filter and No Search
            $query->bind_param('sii', $filter, $startFrom, $recordsPerPage);
        } elseif ($filter !== 'all' and $search !== '') {//Filter and Seach
            $query->bind_param('ssii', $filter, $search, $startFrom, $recordsPerPage);
        } elseif ($filter == 'all' and $search !== '') {//No Filter and Search
            $query->bind_param('sii', $search, $startFrom, $recordsPerPage);
        } else {
            $query->bind_param('ii', $startFrom, $recordsPerPage);
        }


    $query->execute();
    $result = $query->get_result();

    $sqlII = "SELECT * FROM Tenant";
    $queryII = $db->prepare($sqlII);
    $queryII->execute();
    $resultII = $queryII->get_result();
?>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ShiftSync - Users</title>
        <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
        <!-- Add Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet"href="https://elitex.co.za/ProjectC/Styles/styles.css">

    </head>
<body>

<?php

include('../Layout/header.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inline">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control rounded" placeholder="Search by User name"
                            aria-label="Search" aria-describedby="search-addon" />
                    </div>

                    <label for="filter" class="ml-3 mr-2">Filter by Role:</label>
                    <select name="filter" id="filter" class="form-select form-control">
                        <option value="all" <?php echo ($filter === 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="admin" <?php echo ($filter === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo ($filter === 'user') ? 'selected' : ''; ?>>User</option>
                        <!-- Add more options as needed -->
                    </select>

                    <label for="sort" class="ml-3 mr-2">Sort by:</label>
                    <select name="sort" id="sort" class="form-select form-control">
                        <option value="lastname_asc">Last Name (Ascending)</option>
                        <option value="lastname_desc">Last Name (Descending)</option>
                        <!-- Add more sorting options as needed -->
                    </select>

                    <button type="submit" class="btn btn-secondary ml-3 mt-3">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row m-2 p-2">
<table class="table table-bordered">
    <thead class="bg-primary text-white text-center">
        <tr>
            <th style="border-right: none;">User Details</th>
            <th style="border-left: none; border-right: none;">Email</th>
            <th style="border-left: none; border-right: none;">Role</th>
            <th style="border-left: none;">Operations</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";

                // Center-align content in table data cells
                echo '<td style="border-right: none; text-align: center;">' . $row["FirstName"] . ' ' . $row["LastName"] . '</td>';
                echo '<td style="border-left: none; border-right: none; text-align: center;">' . $row["Email"] . '</td>';
                echo '<td style="border-left: none; border-right: none; text-align: center;">' . $row["RoleName"] . '</td>';

                // Details Button
                echo '<td style="border-left: none; text-align: center;"><button class="btn btn-secondary m-3 btn-details" onclick="showUserDetails(' . $row['Id'] . ', \'' . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . '\')">Details</button>';

                // Assign dropdown Button
                echo '<div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-assign dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    ' . ($row['Tenant'] ? $row['TenantName'] : 'Select Tenant') . '
                    </button>
                    <div class="dropdown-menu m-3">';
      
                mysqli_data_seek($resultII, 0);
      
                // Display all tenants as options
                while ($rowII = $resultII->fetch_assoc()) {
                    echo '<a class="dropdown-item" href="#" onclick="onAssignTo(' . $row['Id'] . ',' . $rowII["Id"] . ')">' . $rowII['TenantName'] . '</a>';
                }
      
                echo '</div>
                    </div>';

                // Suspend Button
                if ($row["IsActive"] == 1) {
                    echo '<button class="btn btn-warning m-3 " data-userid="' . $row['Id'] . '" data-action="suspend">Suspend</button></td>';
                    echo "</tr>";
                } else {
                    echo '<button class="btn btn-warning m-3" data-userid="' . $row['Id'] . '" data-action="unsuspend">Un-suspend</button></td>';
                    echo "</tr>";
                }
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>
    </div>
    <!-- Pagination-->
    <?php
    $totalRows = $db->query('SELECT COUNT(*) as count FROM User')->fetch_assoc()['count'];
    $totalPages = ceil($totalRows / $recordsPerPage);

    echo '<nav aria-label="Page navigation">
            <ul class="pagination">';

    // Display the 'First' arrow
    $firstPage = 1;
    $firstDisabled = ($currentPage == $firstPage) ? 'disabled' : '';
    echo '<li class="page-item ' . $firstDisabled . '"><a class="page-link" href="?page=' . $firstPage . '&filter=' . $filter . '" aria-label="First"><span aria-hidden="true">&lt;&lt;</span></a></li>';

    // Display the 'Previous' arrow
    $prevPage = $currentPage - 1;
    $prevDisabled = ($prevPage <= 0) ? 'disabled' : '';
    echo '<li class="page-item ' . $prevDisabled . '"><a class="page-link" href="?page=' . $prevPage . '&filter=' . $filter . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';

    // Display a few pages
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $startPage + 4);

    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . '&filter=' . $filter . '">' . $i . '</a></li>';
    }

    // Display the 'Next' arrow
    $nextPage = $currentPage + 1;
    $nextDisabled = ($nextPage > $totalPages) ? 'disabled' : '';
    echo '<li class="page-item ' . $nextDisabled . '"><a class="page-link" href="?page=' . $nextPage . '&filter=' . $filter . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';

    // Display the 'Last' arrow
    $lastPage = $totalPages;
    $lastDisabled = ($currentPage == $lastPage) ? 'disabled' : '';
    echo '<li class="page-item ' . $lastDisabled . '"><a class="page-link" href="?page=' . $lastPage . '&filter=' . $filter . '" aria-label="Last"><span aria-hidden="true">&gt;&gt;</span></a></li>';

    echo '</ul>
        </nav>';
    ?>
            <div class="text-center">
                <button class="btn btn-primary btn-block m-3" onclick="openAddPage()">Add New User</button>
            </div>
</div>

<!--Pagination -->
            


  <!-- Bootstrap JS -->

<!-- Bootstrap JS -->
<div class="loader"></div>
    <script>
    // Simulate content loading (remove this in your actual implementation)
    window.addEventListener('load', function() {
      // Remove the loader once the page is loaded
      document.querySelector('.loader').style.display = 'none';
    });
  </script>
<script>
        function openAddPage() {
        window.location.href = 'AddUser.php';
    }
//Assiging of Users 
function onAssignTo(userId,tenantId){
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../Functions/assignUserToTenant.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                //alert("User Successfully assigned to tenant...")
                //location.reload();
            }
        };
            xhr.send('userId=' + userId + '&tenantId=' + tenantId);
    }
//Showing of User Details
function showUserDetails(userId, userDetailsJSON) {
    // Parse the JSON string to get the user details object
    var userDetails = JSON.parse(userDetailsJSON);

    // Display user details in the modal
    var userDetailsText = 'First Name: ' + userDetails.FirstName + '<br>' +
                          'Last Name: ' + userDetails.LastName + '<br>' +
                          'Email: ' + userDetails.Email + '<br>' +
                          'Contact Details: ' + userDetails.CellNumber + '<br>' +
                          'Tenant: ' + userDetails.TenantName + '<br>' +
                          'Role: ' + userDetails.RoleName;

    document.getElementById('userDetails').innerHTML = userDetailsText;
    
    // Show the modal
    $('#userDetailsModal').modal('show');
  $('#userDetailsModal').on('hidden.bs.modal'), function () {
        // Clear the user details when the modal is closed
        document.getElementById('userDetails').innerHTML = '';
    };

}
    //Suspending of Users
    document.addEventListener('DOMContentLoaded', function () {
    // Directly bind the click event for buttons with class 'btn-danger'
    var suspendButtons = document.querySelectorAll('.btn-warning');

        suspendButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var userId = button.dataset.userid;
            var action = button.dataset.action;

            // Make an AJAX request to update IsActive status
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../Functions/updateStatus.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Change suspension of user...")
                    location.reload();
                }
            };
            xhr.send('userId=' + userId + '&action=' + action);
        });
    });
});

</script>



<!-- Add this modal structure at the end of your body -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>

            </div>
            <div class="modal-body">
                <!-- User details will be displayed here -->
                <p id="userDetails"></p>
                <p class="align-center">click on Page to close...</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>