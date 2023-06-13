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
    $input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);
$Layer1 = new Layer_Dense(4,5);
$Layer2 = new Layer_Dense(5,2);
$Layer1->forward($input);
$Layer2->forward($Layer1->output);

var_dump($Layer2->output);
//$Layer2->forward($input);

}


function TEST_2(){
list($X, $y) =  np::spiral_data(100, 4);
$Layer1 = new Layer_Dense(2,5);
$Layer2 = new Layer_Dense(5,2);
$Layer1->forward($X);
$activation1 = new Activation_Relu();
$activation1->forward($Layer1->output);
// $testOutput = Test::activation1Check($activation1->output);
//var_dump($activation1->output);

}

function TEST_3(){
list($X, $y) =  np::spiral_data(100, 3);

//var_dump($y);

$Layer1 = new Layer_Dense(2,3);
$Layer1->forward($X);
$activation1 = new Activation_Relu();
$activation1->forward($Layer1->output);


$Layer2 = new Layer_Dense(3,3);
$Layer2->forward($activation1->output);
$activation2 = new Activation_softMax($Layer2->output);
$activation2->forward();


var_dump($activation2->output);

}

function TEST_4(){
  $input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);
$Layer1 = new Layer_Dense(4,3);
$Layer2 = new Layer_Dense(5,2);
$Layer1->forward($input);

$dvalues = array(
    array(1.0, 1.0, 1.0),
    array(2.0, 2.0, 2.0),
    array(3.0, 3.0, 3.0)
);

$Layer1->backward($dvalues);

$activation1 = new Activation_Relu();
$activation1->forward($Layer1->output);

$activation1->backward($dvalues);
//$Layer2->forward($Layer1->output);

}

function TEST_5(){
list($X, $y) =  np::spiral_data(100, 3);

//var_dump($y);$temp = np::diagflat($this->output);

$Layer1 = new Layer_Dense(2,3);
$Layer1->forward($X);
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();


$Layer2 = new Layer_Dense(3,3);
$Layer2->forward($activation1->output);
$activation2 = new Activation_softMax($Layer2->output);
$activation2->forward();
$activation2->backward(array(array(1,2,3,4,5,6)));
}

function TEST_6(){
    $input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);
$dvalues = array(
    array(1.0, 1.0, 1.0),
    array(2.0, 2.0, 2.0),
    array(3.0, 3.0, 3.0)
);
//var_dump($y);$temp = np::diagflat($this->output);

$Layer1 = new Layer_Dense(4,3);
$Layer1->forward($input);
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();


// $Layer2 = new Layer_Dense(3,2);
// $Layer2->forward($activation1->output);
$activation2 = new Activation_softMax($Layer1->output);
$activation2->forward();
$activation2->backward($dvalues);
}


TEST_4();

?>