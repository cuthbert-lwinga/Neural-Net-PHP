<?php
include_once("Headers.php");
include_once("Threads.php");

use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationSigmoid\Activation_Sigmoid;
use ProcessManager\ProcessManager;
use NameSpaceArrayFileHandler\ArrayFileHandler;
use NameSpaceQueue\Queue;
use NameSpaceTaskRegistry\TaskRegistry;
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


function calculateMatrixSize($matrix) {
    $totalBytes = 0;

    foreach ($matrix as $rowIndex => $row) {
        // Convert each element to a string and calculate the length
        foreach ($row as $element) {
            $totalBytes += strlen((string) $element);
        }

        // Add bytes for column separators (','), one less than the number of elements in a row
        if (count($row) > 0) {
            $totalBytes += count($row) - 1;
        }

        // Add bytes for row separator ('@'), except for the last row
        if ($rowIndex < count($matrix) - 1) {
            $totalBytes += 1;
        }
    }

    return $totalBytes;
}
// Example usage:
$rows = 100; // Number of rows
$cols = 100; // Number of columns
$min = 0.1; // Minimum float value
$max = 0.8; // Maximum float value
echo "\nSTARTING PROGRAM....\n";
$matrixA = generateRandomMatrix($rows=300, $cols=1000, $min = 0.1, $max = 0.2) ;
$matrixB = generateRandomMatrix($rows=1000, $cols=1000, $min = 0.1, $max = 0.2) ;



// $matrixB = generateRandomMatrix($rows=512, $cols=512, $min, $max);


$startTime = microtime(true); // Start time
// NumpyLight::dot($matrixA,$matrixB);0.15455293655396 seconds
NumpyLight::dot($matrixA,$matrixB);

            $endTime = microtime(true); // End time
            $executionTime = $endTime - $startTime; // Calculate execution time
            echo "Task dot with normal operation with caller function test executed time $executionTime seconds.\n";





// function testbackground($he=1){
//     mt_srand(); // Seed the random number generator
//     $randomNumber = mt_rand(0, 4);
//     echo "\n $he will sleep for $randomNumber s\n";
//     sleep($randomNumber);
//     echo "\n $he woke up\n";
// }

// Threads::addTask("testbackground",[1]);
// Threads::addTask("testbackground",[2]);
// Threads::addTask("testbackground",[3]);
// Threads::addTask("testbackground",[4]);
// Threads::addTask("testbackground",[5]);
// Threads::addTask("testbackground",[6]);
// Threads::addTask("testbackground",[7]);
// Threads::addTask("testbackground",[8]);
// Threads::run(10);
// echo "\n\n all done \n\n";

?>