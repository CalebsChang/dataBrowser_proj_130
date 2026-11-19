<?php
// OLD JSON VERSION - No longer used.
// This was a test file for editing marathon records in marathon.json.
// The frontend now uses updateMarathon.php for MySQL updates.
require_once 'marathon.php';

//Read Json File
$jsonData = file_get_contents('marathon.json');

//Convert Json into PHP
$marathons = json_decode($jsonData);

$editID = 2;

foreach ($marathons as $marathon) {
    if ($marathon->id == $editID) {
        echo "Found: " . $marathon->raceName . "<br>";

        //update marathon year
        $marathon->year = 2028;
        echo "Updated Year: " . $marathon->year;

    }
}

$jsonOutput = json_encode($marathons, JSON_PRETTY_PRINT);
if ($jsonOutput !== false) {
file_put_contents('marathon.json', $jsonOutput);
echo "<br>Changes saved successfully!";
}
?>