
<?php
// Receive the selected marathon ID from JavaScript
$data = json_decode(file_get_contents('php://input'));

// Set the response format
header('Content-Type: application/json');

// Connect to the MySQL database
include 'db_connect.php';

// Check whether valid JSON was received
if (!$data) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON data."
    ]);
    exit;
}

// Check whether a valid ID was provided
if (
    !isset($data->id) ||
    !filter_var($data->id, FILTER_VALIDATE_INT) ||
    $data->id <= 0
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "A valid marathon ID is required."
    ]);
    $conn->close();
    exit;
}

// Prepare the marathon ID
$deleteID = (int)$data->id;

// SQL query to delete the selected marathon
$sql = "DELETE FROM marathons WHERE id = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Check whether the statement was prepared successfully
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare database query."
    ]);
    $conn->close();
    exit;
}

// Connect the ID variable to the SQL placeholder
$stmt->bind_param("i", $deleteID);

// Execute the SQL query
if ($stmt->execute()) {

    // Check whether a record was actually deleted
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Marathon deleted successfully!"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Marathon not found."
        ]);
    }

} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Unable to delete marathon."
    ]);
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
