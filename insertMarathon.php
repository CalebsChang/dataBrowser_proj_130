<?php
// Receive the marathon information from JavaScript
$raceName = $_POST['raceName'] ?? '';
$location = $_POST['location'] ?? '';
$year = $_POST['year'] ?? '';
$completed = $_POST['completed'] ?? '';
$raceType = $_POST['raceType'] ?? '';

// Set the response format
header('Content-Type: application/json');

// Connection to MySQL
include 'db_connect.php';

// Check required text fields
if (
    empty(trim($raceName)) ||
    empty(trim($location)) ||
    empty(trim($raceType))
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "All required fields must be filled in."
    ]);
    exit;
}

// Validate year
if (
    !filter_var($year, FILTER_VALIDATE_INT) ||
    $year < 2000 ||
    $year > 2100
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Year must be between 2000 and 2100."
    ]);
    exit;
}

// Validate completed Boolean
if ($completed !== 'true' && $completed !== 'false') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Completed must be true or false."
    ]);
    exit;
}

// Prepare the new marathon information
$raceName = trim($raceName);
$location = trim($location);
$year = (int)$year;
$completed = ($completed === 'true') ? 1 : 0;
$raceType = trim($raceType);
$image = "";
// Check whether an image was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

    // Check for upload errors
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "There was an error uploading the image."
        ]);
        exit;
    }

    // Check the image file size (maximum 5 MB)
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Image must be smaller than 5 MB."
        ]);
        exit;
    }

    // Identify the actual file type
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
    finfo_close($fileInfo);

    // Allow only these image types
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Only JPG, PNG, GIF, and WEBP images are allowed."
        ]);
        exit;
    }

    // Create a unique filename
    $fileName = bin2hex(random_bytes(8)) . '.' . $allowedTypes[$mimeType];

    // Set the upload folder
    $uploadDirectory = __DIR__ . '/uploads/';

    // Check that the upload folder exists and is writable
    if (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "The uploads folder is missing or not writable."
        ]);
        exit;
    }

    // Move the uploaded image into the uploads folder
    if (move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDirectory . $fileName
    )) {
        // Save the relative image path for the database
        $image = 'uploads/' . $fileName;
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Unable to save the uploaded image."
        ]);
        exit;
    }
}

// SQL query to insert a new marathon
$sql = "INSERT INTO marathons
        (raceName, location, year, completed, raceType, image)
        VALUES (?, ?, ?, ?, ?, ?)";

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

// Connect the PHP variables to the SQL placeholders
$stmt->bind_param(
    "ssiiss",
    $raceName,
    $location,
    $year,
    $completed,
    $raceType,
    $image
);

// Execute the SQL query
if ($stmt->execute()) {

    // Create the new marathon response
    $newMarathon = new stdClass();

    $newMarathon->id = $conn->insert_id;
    $newMarathon->raceName = $raceName;
    $newMarathon->location = $location;
    $newMarathon->year = $year;
    $newMarathon->completed = (bool)$completed;
    $newMarathon->raceType = $raceType;
    $newMarathon->image = $image;

    echo json_encode([
        "success" => true,
        "marathon" => $newMarathon
    ]);

} else {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to insert marathon into database."
    ]);
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
