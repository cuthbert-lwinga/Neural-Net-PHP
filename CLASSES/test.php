<?php
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationSigmoid\Activation_Sigmoid;
use ProcessManager\ProcessManager;
use NameSpaceArrayFileHandler\ArrayFileHandler;
use NameSpaceQueue\Queue;
use NameSpaceTaskRegistry\TaskRegistry;
// Load matrices from JSON file
// $jsonData = file_get_contents('combined_matrices.json');
// $matrices = json_decode($jsonData, true);

// $matrixA = $matrices['matrixA'];
// $matrixB = $matrices['matrixB'];

// // Perform the dot product
// $startTime = microtime(true); // Start time
// $result = NumpyLight::dot($matrixA, $matrixB);
//  $endTime = microtime(true); // End time
//             $executionTime = $endTime - $startTime; // Calculate execution time
//             echo "Task dot executed time $executionTime seconds.\n";
// var_dump($result);
// $Activation_Sigmoid->forward($temp);

// $Activation_Sigmoid->backward($temp);

// NumpyLight::displayMatrix($Activation_Sigmoid->dinputs);

// NumpyLight::displayMatrix($Activation_Sigmoid->dinputs);

// $inf = (NumpyLight::log([0,0,0,0]));

// // var_dump(NumpyLight::clip($inf, 1e-7, 1 - 1e-7));


// var_dump(NumpyLight::reshape([0,0,0,0,0,0],[-1,1]));


// $matrixA = generateRandomMatrix($rows=300, $cols=1000, $min = 0.1, $max = 0.2) ;
// $matrixB = generateRandomMatrix($rows=1000, $cols=1000, $min = 0.1, $max = 0.2) ;
// $startTime = microtime(true); // Start time

$matrixA = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];  // 3x3 matrix
$matrixB = [[10, 11], [12, 13], [14, 15]];  // 3x2 matrix
var_dump(NumpyLight::dot($matrixA,$matrixB));
// echo "\n+++++++++++++++++++++++++++++++++++++++++++++\n";

// $matrixA = [[0, 0], [0, 0]];
// $matrixB = [[4, 3], [2, 1]];
// var_dump(NumpyLight::dot($matrixA,$matrixB));

// $endTime = microtime(true); // End time
// $executionTime = $endTime - $startTime; // Calculate execution time
// echo "SINGLE-THREADED[$executionTime seconds]\n";


?>
