<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


function TEST_1(){
list($X, $y) =  np::spiral_data(100, 3);

$softmax_outputs = [
    [0.7, 0.1, 0.2],
    [0.1, 0.5, 0.4],
    [0.02, 0.9, 0.08]
];

$class_targets = [0, 1, 1];

$softmax_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
$softmax_loss->backward($softmax_outputs,$class_targets);
$dvalues1 = $softmax_loss->dinputs;

$activation = new Activation_Softmax();
$activation->output = $softmax_outputs;
$loss = new Loss_CategoricalCrossentropy();

$loss->backward($softmax_outputs,$class_targets);
$activation->backward($loss->dinputs);
$dvalue2 = $activation->dinputs;

echo "Gradients: combined loss and activation: \n";
var_dump($dvalues1);
echo "Gradients: separate loss and activation: \n";
var_dump($dvalue2);




// $Layer1 = new Layer_Dense(2,3);
// $Layer1->forward($X);
// $activation1 = new Activation_Relu($Layer1->output);
// $activation1->forward();


// $Layer2 = new Layer_Dense(3,3);
// $Layer2->forward($activation1->output);
// $activation2 = new Activation_softMax($Layer2->output);
// $activation2->forward();
// $activation2->backward(array(array(1,2,3,4,5,6)));
}

function TEST_2(){

$dinputs = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6], [0.7, 0.8, 0.9]];
$y_true = [0, 1, 2];
$value = 1;

$result = np::subtractFromDInputs($dinputs, $y_true, $value);
var_dump($result);
}
TEST_1();

?>