<?php
ini_set('memory_limit', '20480M'); // Increase the memory limit to 20480MB (20GB)

include_once("../../CLASSES/Headers.php");
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


$rows = 2000; // Number of rows
$cols = 2000; // Number of columns
$min = 0.0000001; // Minimum float value
$max = 0.0000008; // Maximum float value


$matrixA = generateRandomMatrix($rows, $cols, $min, $max);

$startTime = microtime(true); // Start time 3.8503890037537 seconds.
$dotproductOutput = (NumpyLight::dot($matrixA,$matrixA));
$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";


// $startTime = microtime(true); // Start time 3.8503890037537 seconds.
// $dotproductOutput = (NumpyLight::jacobiansdf_matrix($matrixA,$matrixA));
// $endTime = microtime(true); // End time
// $executionTime = $endTime - $startTime; // Calculate execution time
// echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";

// var_dump($dotproductOutput)
?>