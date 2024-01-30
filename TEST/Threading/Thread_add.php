<?php
ini_set('memory_limit', '102400M'); // Increase the memory limit to 102400MB (100GB)

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


$rows = 60000; // Number of rows
$cols = 700; // Number of columns
$min = 0.01; // Minimum float value
$max = 0.08; // Maximum float value


$matrixA = generateRandomMatrix($rows, $cols, $min, $max);
$matrixB = $matrixA;//generateRandomMatrix($rows, $cols, $min, $max);

echo "\n\n DATA GENERATED \n\n";

$startTime = microtime(true); // Start time 3.8503890037537 seconds.
$dotproductOutput = (NumpyLight::add($matrixA,$matrixB,false));
$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "Task add in php time $executionTime seconds.\n";


$startTime = microtime(true); // Start time 3.8503890037537 seconds.
$dotproductOutput = (NumpyLight::divide($matrixA,$matrixB,true));
$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";
// var_dump($dotproductOutput)
?>