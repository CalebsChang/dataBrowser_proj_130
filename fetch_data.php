<?php
header('Content-Type: application/json');
// Include the database connection
include 'db_connect.php';

// SQL query to retrieve all marathon records
$sql = "SELECT * FROM marathons";

// Execute the query
$result = $conn->query($sql);

// Check whether the query was successful
if ($result) {

    // Store all retrieved records in an array
    $marathons = [];

    while ($row = $result->fetch_assoc()) {
        $marathons[] = $row;
    }

    // Return the records as JSON
    header('Content-Type: application/json');
    echo json_encode($marathons, JSON_PRETTY_PRINT);

} else {
    // Display an error if the query fails
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error fetching marathon records: " . $conn->error
    ]);
}

// Close the database connection
$conn->close();
?>
