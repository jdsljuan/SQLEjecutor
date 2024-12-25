<?php
// Configuration for database connection
$serverName = $_POST['serverName'].'\\'.$_POST["serverInstance"];
$database = $_POST['dbName']; // Replace with your database name
$username = $_POST['dbUser']; // Replace with your username
$password = $_POST['dbPass']; // Replace with your password

// Establish the connection
try {
    $connectionOptions = [
        "Database" => $database,
        "UID" => $username,
        "PWD" => $password,
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        throw new Exception("Connection failed: " . print_r(sqlsrv_errors(), true));
    }

    // Check if the POST request contains an SQL query
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sql_query"])) {
        $sqlQuery = $_POST["sql_query"];

        // Prepare and execute the query
        $stmt = sqlsrv_prepare($conn, $sqlQuery);
        if ($stmt === false) {
            throw new Exception("SQL preparation failed: " . print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_execute($stmt)) {
            //echo "Query executed successfully.";
            
            // Fetch results if it's a SELECT query
            $result = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $result[] = $row;
            }

            // Display results as JSON
            echo json_encode($result);
        } else {
            throw new Exception("Query execution failed: " . print_r(sqlsrv_errors(), true));
        }
    } else {
        echo "No SQL query provided.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Close the connection
    if (isset($conn) && $conn !== false) {
        sqlsrv_close($conn);
    }
}
?>
