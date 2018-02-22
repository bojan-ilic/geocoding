<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User's details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="details">
    <h2>Users Details</h2>
    <a href="index.php" class="button">Add New User</a>
    <?php
    // Include config file
    require_once 'config.php';

    // Attempt select query execution
    $sql = "SELECT * FROM users";
    if($result = $pdo->query($sql)){
        if($result->rowCount() > 0){
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>#</th>";
            echo "<th>First name</th>";
            echo "<th>Last name</th>";
            echo "<th>Street/Number</th>";
            echo "<th>City</th>";
            echo "<th>Country</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while($row = $result->fetch()){
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['first_name'] . "</td>";
                echo "<td>" . $row['last_name'] . "</td>";
                echo "<td>" . $row['street'] . "</td>";
                echo "<td>" . $row['city'] . "</td>";
                echo "<td>" . $row['country'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            // Free result set
            unset($result);
        } else{
            echo "<p class='lead'><em>No records were found.</em></p>";
        }
    } else{
        echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
    }

    // Close connection
    unset($pdo);
    ?>
</div>
</body>
</html>