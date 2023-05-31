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
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();
$testOutput = Test::activation1Check($activation1->output);
var_dump($testOutput);

}

function TEST_3(){
list($X, $y) =  np::spiral_data(100, 3);
$Layer1 = new Layer_Dence(2,3);
$Layer1->foward($X);
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();


$Layer2 = new Layer_Dence(3,3);
$Layer2->foward($activation1->output);
$activation2 = new Activation_softMax($Layer2->output);
$activation2->forward();


var_dump($activation2->output);

}


TEST_3();

?>