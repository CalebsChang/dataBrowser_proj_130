<?php
//Will read the marathon.php file once, can cause problems if read multiple times 
require_once 'marathon.php';

// Read the JSON file
$jsonData = file_get_contents('marathon.json');

// Json text into Php object
$marathons = json_decode($jsonData);

// Finding highest id
$highID = 0;
foreach ($marathons as $marathon) {
    if ($marathon-> id > $highID) {
        $highID = $marathon->id;
    }
}
$newID = $highID + 1;

// Create Marathon Obj
$newMarathon = new Marathon(
    $newID,
    "Los Angeles Marathon",
    "California, USA",
    2027,
    false,
    "World Major",
    "uploads/losAngeles.jpg"
);

// Add New marathons into marathons
$marathons[] = $newMarathon;

$jsonOutput = json_encode($marathons, JSON_PRETTY_PRINT);
file_put_contents('marathon.json', $jsonOutput);
echo "New Marathon Added!";

// Display the contents
foreach ($marathons as $marathon) {
    // Create Marathon Obj from JSON record
    $race = new Marathon(
        $marathon->id, 
        $marathon->raceName,
        $marathon->location,
        $marathon->year,
        $marathon->completed,
        $marathon->raceType,
        $marathon->image
    );

    // Display the Marathon Obj's Properties
    echo "ID: " . $marathon->id . "<br>";
    echo "Race Name: " . $marathon->raceName . "<br>";
    echo "Location: " . $marathon->location . "<br>";
    echo "Year: " . $marathon->year . "<br>";
    echo "<hr>";

}

// Php into JSON
$jsonOutput = json_encode($marathons);
// Display JSON
echo "<pre>";
echo $jsonOutput;
echo "</pre>";

?>
