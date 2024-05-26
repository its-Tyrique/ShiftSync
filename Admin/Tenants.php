<?php
session_start();

?>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ShiftSync - Tenants</title>
        <link rel="icon" href="https://elitex.co.za/ProjectC/Assets/Logo.png" type="image/x-icon">
        <!-- Add Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet"href="https://elitex.co.za/ProjectC/Styles/styles.css">
    </head>
<body>

<?php

  include('../Layout/header.php');

  include("../Database/connect.php");
 	 $db = $mysqli;
	$recordsPerPage = 5;

// Determine current page
	$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the starting record for the current page
	$startFrom = ($currentPage - 1) * $recordsPerPage;


$sql = "SELECT * FROM Tenant";
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    if (!empty($searchTerm)) {
        $sql .= " WHERE TenantName LIKE ?";
    }
$query = $db->prepare($sql);

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'tenant_name_asc';

switch ($sort) {
    case 'tenant_name_asc':
        $sql .= ' ORDER BY TenantName ASC';
        break;
    case 'tenant_name_desc':
        $sql .= ' ORDER BY TenantName DESC';
        break;
    // Add more sorting options as needed
}

$sql .= ' LIMIT ?, ?';
$query = $db->prepare($sql);
if (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%';
    $query->bind_param('ssi', $searchTerm, $startFrom, $recordsPerPage);
} else {
    $query->bind_param('ii', $startFrom, $recordsPerPage);
}


$query->execute();
$result = $query->get_result();

?>
<div class="container mt-2">
    <div class="filter-sort-container">
        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inline">
            <div class="form-group">
            <div class="input-group">
                        <input type="search" name="search" class="form-control rounded" placeholder="Search by tenant Name"
                            aria-label="Search" aria-describedby="search-addon" />
                        </div>
            </div>
			<div class="form-group">
                <label for="sort" class="mr-2">Sort by:</label>
                <select name="sort" id="sort" class="form-control">
                    <option value="tenant_name_asc">Tenant Name (Ascending)</option>
                    <option value="tenant_name_desc">Tenant Name (Descending)</option>
                    <!-- Add more sorting options as needed -->
                </select>
            </div>
            <button type="submit" class="btn btn-secondary m-3">Apply</button>
        </form>
    </div>
</div>

<div class="row m-2">
    <table class="table table-bordered ">
      <thead class="bg-primary text-white text-center">
        <tr>
          <th>Tenant Name</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo '<td style="text-align: center">' .$row["TenantName"] . '</td>';
                echo '<td style="text-align: center"><button class="btn btn-secondary btn-details" onclick="TenantDetails(' . $row["Id"] . ', \'' . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . '\')">Edit</button>';
                                
				echo "</tr>";
			}
        } else {
            echo "<tr><td colspan='3' >No records found</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <?php
    $totalRows = $db->query('SELECT COUNT(*) as count FROM Tenant')->fetch_assoc()['count'];
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
    </div>
<!--Pagination -->

    <div class="text-center">
    <button class="btn btn-secondary btn-block m-2" onclick="openAddPage()">Add New Tenant</button>
    </div>
<!--Modal-->
<!-- Bootstrap JS -->
    <script>
    // Simulate content loading (remove this in your actual implementation)
    window.addEventListener('load', function() {
      // Remove the loader once the page is loaded
      document.querySelector('.loader').style.display = 'none';
    });

    function TenantDetails(RequestId, TenantDetailsJSON) {
    try {
        // Parse the JSON string to get the user details object
        var Details = JSON.parse(TenantDetailsJSON);

        // Encode the details as URL parameters
        var encodedDetails = encodeURIComponent(JSON.stringify(Details));

        // Redirect to another page with the details as URL parameters
        window.location.href = '../tenantDetails.php?tenantId=' + RequestId + '&details=' + encodedDetails;
    } catch (error) {
        console.error('Error parsing JSON:', error);
        alert('Error parsing JSON. Please check the console for details.');
    }
}
  </script>
<!-- Add this modal structure at the end of your body -->
<script>
	 function openAddPage() {
        window.location.href = '../Functions/AddTenant.php';
    }
	</script>
</body>
</html>