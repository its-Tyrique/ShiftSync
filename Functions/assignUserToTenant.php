<?php
include("../Database/connect.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['userId'];
    $tenantId = $_POST['tenantId'];

    // Perform the SQL update to set TenantId in the User table
    $updateSql = 'UPDATE User SET Tenant = ? WHERE Id = ?';
    $updateQuery = $mysqli->prepare($updateSql);
    $updateQuery->bind_param('ii', $tenantId, $userId);
    
    if ($updateQuery->execute()) {
        echo 'User assigned to tenant successfully.';
    } else {
        echo 'Error assigning user to tenant.';
    }

    $updateQuery->close();
}
?>
