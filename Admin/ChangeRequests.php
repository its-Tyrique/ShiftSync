<?php
session_start();
$user = $_SESSION['user'];
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	error_reporting(-1);

include("../Database/connect.php");
$db = $mysqli;

$recordsPerPage = 5;

// Determine the current page
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the starting record for the current page
$startFrom = ($currentPage - 1) * $recordsPerPage;

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
//For the search Button
$search = isset($_GET['search']) ? $_GET['search'] : '';


$sql = 'SELECT ChangeRequest.*, RequestType.TypeName, User.FirstName, User.LastName, Priority.Priority, Status.Name
        FROM ChangeRequest
        JOIN RequestType ON ChangeRequest.RequestType = RequestType.Id
        JOIN User ON ChangeRequest.User = User.Id
        JOIN Priority ON ChangeRequest.Priority = Priority.Id
        JOIN Status ON ChangeRequest.Status = Status.Id';

if ($filter !== 'all') {
    $sql .= ' WHERE RequestType.TypeName = ?';
}

// Accompany sql for search
if (!empty($search)) {
    // If a WHERE clause already exists, add AND, otherwise add WHERE
    $sql .= (strpos($sql, 'WHERE') !== false) ? ' AND ChangeRequest.Title LIKE ?' : ' WHERE ChangeRequest.Title LIKE ?';
}

if ($user["Role"] == 1) {
    // No additional conditions needed for admin
} else {
    // Add a condition for regular users based on their "Tenent"
    $sql .= ' AND User.Tenant = ?';
}

$sql .= ' LIMIT ?, ?';

$query = $db->prepare($sql);


// Check if the user's role is 1 

// Bind parameters based on the user's role
        if ($user["Role"] == 1 && $filter !== 'all' && $search =='') {
            $query->bind_param('sii', $filter, $startFrom, $recordsPerPage);
        } elseif ($user["Role"] == 1 && $filter !== 'all' and $search !== '') {
            $query->bind_param('ssii', $filter, $search, $startFrom, $recordsPerPage);
        } elseif ($user["Role"] == 1 && $filter == 'all' and $search !== '') {
            $query->bind_param('sii', $search, $startFrom, $recordsPerPage);
        } elseif ($user["Role"] == 1) {
            $query->bind_param('ii', $startFrom, $recordsPerPage);
        } elseif ($filter !== 'all' && $search =='') {
            $query->bind_param('siii', $filter, $user["Tenant"], $startFrom, $recordsPerPage);
        } elseif ($filter !== 'all' and $search !== '') {
            $query->bind_param('ssiii', $filter, $search, $user["Tenant"], $startFrom, $recordsPerPage);
        } elseif ($filter == 'all' and $search !== '') {
            $query->bind_param('siii', $search, $user["Tenant"], $startFrom, $recordsPerPage);
        } else {
            $query->bind_param('iii', $user["Tenant"], $startFrom, $recordsPerPage);
        }

$query->execute();
$result = $query->get_result();

?>
<!-- Rest of your HTML code remains unchanged -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShiftSync - Change Requests</title>
    <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://elitex.co.za/ProjectC/Styles/styles.css">
</head>

<body>

    <?php include('../Layout/header.php'); ?>
        <div class="container-fluid">
            <div class="filter-sort container">
            <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inline">
                <!-- Inside the form -->
            <div class="form-group">
                <div class="input-group">
                    <input type="search" name="search" class="form-control rounded" placeholder="Search by Title" aria-label="Search" aria-describedby="search-addon" />
                    <button type="submit" class="btn btn-outline-primary" data-mdb-ripple-init>Search</button>
                </div>
            </div>

                
                    <div class="form-group">
                        <label for="filter" class="mr-2">Request Type:</label>
                        <select name="filter" id="filter" class="form-control">
                            <option value="all" <?php echo ($filter === 'all') ? 'selected' : ''; ?>>All</option>
                            <option value="Bug" <?php echo ($filter === 'Bug') ? 'selected' : ''; ?>>Bug</option>
                            <option value="New Request" <?php echo ($filter === 'New Request') ? 'selected' : ''; ?>>New Request</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary m-3">Apply Filter</button>
            </form>
        </div>
        <div class="row m-2 p-2">
                <table class="table">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th>Title</th>
                            <th>Requested By</th>
                            <th>Requested On</th>
                            <th>Expected Delivery Date</th>
                            <th>Request Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo '<td style="text-align: center">' . $row["Title"] . '</td>';
                                echo '<td style="text-align: center">' . $row["FirstName"]. ' '. $row["LastName"] . '</td>';
                                echo '<td style="text-align: center">' . $row["RequestDate"] . '</td>';
                                echo '<td style="text-align: center">' . $row["DateExpected"] . '</td>';
                                echo '<td style="text-align: center">' . $row["TypeName"] . '</td>';
                                echo '<td style="text-align: center">' . $row["Priority"] . '</td>';
                                echo '<td style="text-align: center">' . $row["Name"] . '</td>';
                                
                                echo '<td style="text-align: center"><button class="btn btn-secondary m-3 btn-details" onclick="showRequestDetails(' . $row["Id"] . ', \'' . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . '\')"
                                )">Details</button>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
   <!-- Pagination -->
   <?php
            $totalRows = $db->query('SELECT COUNT(*) as count FROM ChangeRequest')->fetch_assoc()['count'];
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
            <button type="button" class="btn btn-secondary" onclick="addChangeRequest()">Add Change Request</button>
   </div>
        </div>

                 </div>

<script>
function showRequestDetails(RequestId, RequestDetailsJSON) {
    //console.log('RequestDetailsJSON:', RequestDetailsJSON);
    try {

//        console.log('RequestDetailsJSON:', RequestDetailsJSON);
        var decodedDetails = decodeURIComponent(RequestDetailsJSON);
        // Parse the JSON string to get the user details object
        var Details = JSON.parse(decodedDetails);

        // Encode the details as URL parameters
        var encodedDetails = encodeURIComponent(JSON.stringify(Details));

        // Redirect to another page with the details as URL parameters
        window.location.href = '../ChangeRequestDetails.php?requestId=' + RequestId + '&details=' + encodedDetails;
    } catch (error) {
        console.error('Error parsing JSON:', error);
        alert('Error parsing JSON. Please check the console for details.');
    }
}

function addChangeRequest() {
    window.location.href = "../Functions/addChangeRequest.php";
}
</script>


</body>

</html>
