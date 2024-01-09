<?php
// ini_set('memory_limit', '20480M'); // Increase the memory limit to 20480MB (20GB)
include_once("SharedMemoryHandler.php");
include_once("Threads.php");
include_once("MT_Maxtrix.php");
include_once("NumpyLight.php");
include_once("SharedFile.php");
use NameSpaceSharedFile\SharedFile;
use NameSpaceMT_Matrix\MT_Maxtrix;
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceThreads\Threads;

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

Threads::init();
// Threads::init();
// Example usage:0.14973092079163 seconds.
$rows = 200; // Number of rows
$cols = 28*28; // Number of columns
$min = 0.1; // Minimum float value
$max = 0.8; // Maximum float value
echo "starting\n";

$jsonData = [];

$matrixA = generateRandomMatrix($rows, $cols, $min = 0.1, $max = 0.2);
$matrixB = generateRandomMatrix($rows=28*28, $cols=200, $min = 0.1, $max = 0.2);//generateRandomMatrix($rows, $cols, $min = 0.1, $max = 0.2) ;

// $jsonData["matrixA"] = $matrixA;
// $jsonData["matrixB"] = $matrixA;
// $jsonData["output"] = $dotproductOutput;

// $filePath = 'test.json';

// // Save JSON data to the file
// file_put_contents($filePath, json_encode($jsonData));
// $Threads = new Threads();

// $mt = new MT_Maxtrix();


    $startTime = microtime(true); // Start time 3.8503890037537 seconds.

    $dotproductOutput = (Threads::execute($matrixA,$matrixB,"dot"));
    $endTime = microtime(true); // End time
    $executionTime = $endTime - $startTime; // Calculate execution time
    echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";




$startTime = microtime(true); // Start time 3.8503890037537 seconds.

(NumpyLight::dot($matrixA,$matrixB));

$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";



// echo "\nDONE\n";

// echo "\n+++++++++++++++++++++++++++++++++++++++++++++\n";

// $matrixA = [[0, 0], [0, 0]];
// $matrixB = [[4, 3], [2, 1]];
// $mt = new MT_Maxtrix();
// $startTime = microtime(true); // Start time 3.8503890037537 seconds.

// var_dump($mt->dot($matrixA,$matrixB,6));

// $endTime = microtime(true); // End time
// $executionTime = $endTime - $startTime; // Calculate execution time
// echo "Task dot with threading operation with caller function test executed time $executionTime seconds.\n";


?>