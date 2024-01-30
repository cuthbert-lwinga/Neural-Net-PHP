<?php
ini_set('memory_limit', '20480M'); // Increase the memory limit to 20480MB (20GB)

include_once("NumpyLight.php");


use NameSpaceNumpyLight\NumpyLight as NumpyLight;

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

// Function to write data to a CSV file
function writeCSV($data, $filename = 'matrix_performance.csv') {
    $file = fopen($filename, 'w');
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    fclose($file);
}

// Matrix sizes to test
$matrixSizes = [];


for ($i=1; $i < 500; $i++) { 
    $matrixSizes[] = $i*10;
}


// Initialize an array to store results
$results = [];
$results[] = ['Matrix Size', 'Save Time', 'Dot Product Time'];

foreach ($matrixSizes as $size) {
    $matrixA = generateRandomMatrix($size, $size, 0.1, 0.2);
    $array = ["a" => $matrixA, "b" => $matrixA];
    echo "\n\n SIZE: $size \n\n";
    // Measure the time for saving the matrix
    $startTime = microtime(true);
    file_put_contents("matrix.json", json_encode($array));
    $endTime = microtime(true);
    $saveTime = $endTime - $startTime;
    echo "Saving executed time $saveTime seconds.\n";

    // Measure the time for dot product
    $startTime = microtime(true);
    $dotproductOutput = NumpyLight::dot($matrixA, $matrixA);
    $endTime = microtime(true);
    $dotProductTime = $endTime - $startTime;
    echo "Dot product executed time $dotProductTime seconds.\n";

    // Store the results
    $results[] = [$size, $saveTime, $dotProductTime];
}

// Write results to CSV
writeCSV($results,$filename = "matrix_performance_with_c_implimentaion.csv");

?>
