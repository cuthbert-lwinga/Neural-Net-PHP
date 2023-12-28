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


function extractRowAndColumn($matrixA, $matrixB, $i) {
    // Extract the ith row from matrixA
    $row = $matrixA[$i];

    // Extract the ith column from matrixB
    $column = array_column($matrixB, $i);

    return [$row, $column];
}


function calculateDotProduct($array1, $array2) {
    $sum = 0;
    $length = count($array1);
    for ($i = 0; $i < $length; $i++) {
        $sum += $array1[$i] * $array2[$i];
    }
    return $sum;
}

// Example usage
$matrixA = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
    [7, 8, 9]
];
$matrixB = [
    [9, 8, 7],
    [6, 5, 4],
    [3, 2, 1]
];

$output = [];
$startTime = microtime(true); // Start time

$shapeA = NumpyLight::shape($matrixA);
$shapeB = NumpyLight::shape($matrixB);

for ($i=0; $i < $shapeA[0]; $i++) { 
    $rowOuput = [];
    $row = NumpyLight::extractRow($matrixA,0);
    for ($j=0; $j < $shapeB[1]; $j++) { 
        $column = NumpyLight::extractColumn($matrixB,$j);
        $rowOuput[] = NumpyLight::dotProduct($row, $column);
    }
    $output[] = $rowOuput;
}

 $endTime = microtime(true); // End time
            $executionTime = $endTime - $startTime; // Calculate execution time
            echo "Task dot with normal operation with caller function test executed time $executionTime seconds.\n";


$startTime = microtime(true); // Start time

// NumpyLight::dot($matrixA,$matrixB);

 for ($i=0; $i < 10; $i++){
    testingFunc($i, ($i*2));
 }

$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "SINGLE-THREADED[$executionTime seconds]\n";


// $startTime = microtime(true); // Start time

// NumpyLight::parallelDotProducttest($matrixA,$matrixB,$shapeA,$shapeB);

// $endTime = microtime(true); // End time
// $executionTime = $endTime - $startTime; // Calculate execution time
// echo "Task dot with threads executed time $executionTime seconds.\n";



function testingFunc($arg1, $arg2) {
    // $result = 1;

    // // Example: Calculating factorial of $arg1
    // for ($i = $arg1; $i > 1; $i--) {
    //     $result *= $i;
    // }

    // // Additional operation using $arg2
    // $result += $arg2;

    sleep(1);
    // echo "\n Output($arg1):: $result\n";
}



$ProcessManager = new ProcessManager(20);
$b =4;
 $a = 2;

$startTime = microtime(true); // Start time

 for ($i=0; $i < 10; $i++){
    $ProcessManager->addTask('testingFunc', [$i, ($i*2)]);
 }


$ProcessManager->waitForAllProcesses();


$endTime = microtime(true); // End time
$executionTime = $endTime - $startTime; // Calculate execution time
echo "MULTI-THREADED[$executionTime seconds].\n";


// echo "\n DONE \n";
// sleep(20);
echo "\n\n Starting shutdown \n\n";
// sleep(10);
$ProcessManager->killProcesses();
echo "\n\n SHUTDOWN \n\n";


// // Set a custom file name for the task registry (optional)
// TaskRegistry::setTaskFileName('my_custom_tasks.txt');

// // Generate a unique key for the task
// $taskKey = TaskRegistry::generateUniqueKey();
// $taskKey2 = TaskRegistry::generateUniqueKey();

// // Add the task to the registry
// TaskRegistry::addTask($taskKey, 'NameSpaceNumpyLight\\NumpyLight::shape', [[10, 20]]);
// TaskRegistry::addTask($taskKey2, 'NameSpaceNumpyLight\\NumpyLight::shape', [[100, 200]]);

// // Retrieve and execute the task
// $taskData = TaskRegistry::getTask($taskKey);
// if ($taskData !== null) {
//     // var_dump($taskData);
//     $result = call_user_func_array(($taskData['function']), $taskData['arguments']);
//     var_dump($result);
// }

// $taskData = TaskRegistry::getTask($taskKey2);
// if ($taskData !== null) {
//     // var_dump($taskData);
//     $result = call_user_func_array(($taskData['function']), $taskData['arguments']);
//     var_dump($result);
// }


// var_dump($output);


?>
