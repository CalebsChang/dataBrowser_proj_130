<?php
// Test file 
// Include our Marathon class
require_once 'marathon.php';

// Create a new Marathon object
$race1 = new Marathon(
    "Two Cities Marathon",
    "Fresno, California",
    2026,
    true,
    "Regional",
    "uploads/two-cities.jpg"
);
$race2 = new Marathon(
    "Sydney Marathon",
    "Sydney, Australia",
    2027,
    false,
    "World Major",
    "uploads/sydney.jpg"
);

// Display the race name
echo "Race Name: " . $race1->raceName . "<br>";
echo "Location: " . $race1->location . "<br>";
echo "Year: " . $race1->year . "<br>";
echo "Completed: " . ($race1->completed ? "Yes" : "No") . "<br>";
echo "Race Type: " . $race1->raceType . "<br>";
echo "Image: " . $race1->image . "<br>";

echo "<hr>";

echo "Race Name: " . $race2->raceName . "<br>";
echo "Location: " . $race2->location . "<br>";
echo "Year: " . $race2->year . "<br>";
echo "Completed: " . ($race2->completed ? "Yes" : "No") . "<br>";
echo "Race Type: " . $race2->raceType . "<br>";
echo "Image: " . $race2->image . "<br>";
?>