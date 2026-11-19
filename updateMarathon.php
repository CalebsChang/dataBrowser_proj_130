<?php
// Receive the edited marathon information from JavaScript
$id = $_POST['id'] ?? '';
$raceName = $_POST['raceName'] ?? '';
$location = $_POST['location'] ?? '';
$year = $_POST['year'] ?? '';
$completed = $_POST['completed'] ?? '';
$raceType = $_POST['raceType'] ?? '';

// Set the response format
header('Content-Type: application/json');
// Connect to the MySQL database
include 'db_connect.php';

// Check whether an ID was provided
if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "A valid marathon ID is required."
    ]);
    exit;
}

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

// Prepare the updated marathon information
$id = (int)$id;
$raceName = trim($raceName);
$location = trim($location);
$year = (int)$year;
$completed = ($completed === 'true') ? 1 : 0;
$raceType = trim($raceType);

// Get the existing marathon image
$imageSql = "SELECT image FROM marathons WHERE id = ?";
$imageStmt = $conn->prepare($imageSql);

if (!$imageStmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve existing marathon image."
    ]);
    $conn->close();
    exit;
}

$imageStmt->bind_param("i", $id);
$imageStmt->execute();

$imageResult = $imageStmt->get_result();
$existingMarathon = $imageResult->fetch_assoc();

// Check whether the marathon exists
if (!$existingMarathon) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Marathon not found."
    ]);
    $imageStmt->close();
    $conn->close();
    exit;
}

// Preserve the existing image by default
$image = $existingMarathon['image'];

// Check whether a new image was uploaded
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

    // Check image file size (maximum 5 MB)
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

    // Move the new image into the uploads folder
    if (move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDirectory . $fileName
    )) {
        // Replace the old image path with the new image path
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

$imageStmt->close();

// SQL query to update an existing marathon
$sql = "UPDATE marathons
        SET raceName = ?, location = ?, year = ?,
            completed = ?, raceType = ?, image = ?
        WHERE id = ?";

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

// Connect PHP variables to the SQL placeholders
// s (string) , i (integer), NOTE completed is an i due to 0 and 1 being true and false
$stmt->bind_param(
    "ssiissi",
    $raceName,
    $location,
    $year,
    $completed,
    $raceType,
    $image,
    $id
);
// Execute the SQL update
if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Marathon updated!",
        "image" => $image
    ]);

} else {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database update failed."
    ]);
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>