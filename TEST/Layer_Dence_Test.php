<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


$input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);








// // Display the data
// for ($i = 0; $i < count($X); $i++) {
//     echo "X[$i]: " . implode(', ', $X[$i]) . " - y[$i]: " . $y[$i] . "\n";
// }

function TEST_1(){
$Layer1 = new Layer_Dence(2,5);
$Layer2 = new Layer_Dence(5,2);
$Layer1->foward($input);
$Layer2->foward($Layer1->output);

var_dump($Layer2->output);
//$Layer2->foward($input);

}


function TEST_2(){
list($X, $y) =  np::spiral_data(100, 4);
$Layer1 = new Layer_Dence(2,5);
$Layer2 = new Layer_Dence(5,2);
$Layer1->foward($X);
$relu = new Activation_ReLU($Layer1->output);
$relu->forward();
$testOutput = Test::reluCheck($relu->output);
var_dump($testOutput);

}

TEST_2();

?>