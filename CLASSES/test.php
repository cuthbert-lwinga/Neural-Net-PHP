<?php
ini_set('memory_limit', '20480M'); // Increase the memory limit to 20480MB (20GB)

include_once("NumpyLight.php");
use NameSpaceNumpyLight\NumpyLight;


function generateRandomMatrix($rows, $cols, $min = 0.0, $max = 1.0) {
    $matrix = [];

    for ($i = 0; $i < $rows; $i++) {
        $row = [];
        for ($j = 0; $j < $cols; $j++) {
            $row[] = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        }
        $matrix[] = $row;
    }

    return $matrix;
}

$rows = 100; // Number of rows
$cols = 100; // Number of columns
$min = 0.1; // Minimum float value
$max = 0.8; // Maximum float value
echo "starting\n";

$jsonData = [];

$matrixA = generateRandomMatrix($rows, $cols, $min = 0.1, $max = 0.2);

$startTime = microtime(true); // Start time 3.8503890037537 seconds.

(NumpyLight::dot($matrixA,$matrixA));

$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";



?>