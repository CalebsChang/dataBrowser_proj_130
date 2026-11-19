<?php
// OLD JSON VERSION - No longer used.
// The frontend now retrieves marathon records from fetch_data.php.
$jsonData = file_get_contents('marathon.json');
header('Content-Type: application/json');
echo $jsonData;
?>