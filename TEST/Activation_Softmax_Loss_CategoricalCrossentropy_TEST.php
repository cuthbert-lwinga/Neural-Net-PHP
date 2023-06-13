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
np::printMatrix($dvalues1);
echo "Gradients: separate loss and activation: \n";
np::printMatrix($dvalue2);

}

function TEST_2(){

$dinputs = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6], [0.7, 0.8, 0.9]];
$y_true = [0, 1, 2];
$value = 1;

$result = np::subtractFromDInputs($dinputs, $y_true, $value);
var_dump($result);
}

function TEST_3(){
list($X, $y) =  np::spiral_data(100, 3);

$dense1 = new Layer_Dense(2,3);

$activation1 = new Activation_Relu();

$dense2 = new Layer_Dense(3,3);

$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

$dense1->forward($X);

$activation1->forward($dense1->output);

$dense2->forward($activation1->output);

$loss = $loss_activation->forward($dense2->output,$y);

np::printMatrix($loss_activation->output,5);

$acc = np::accuracy($loss_activation->output,$y);

echo "acc: $acc , loss: $loss\n";


$loss_activation->backward($loss_activation->output,$y);
$dense2->backward($loss_activation->dinputs);
$activation1->backward($dense2->dinputs);
$dense1->backward($activation1->dinput);

echo "\n";
np::printMatrix($dense1->dweights);
echo "\n";
np::printMatrix($dense1->dbiases);
echo "\n";
np::printMatrix($dense2->dweights);
echo "\n";
np::printMatrix($dense2->dbiases);


}



function TEST_4(){
list($X, $y) =  np::spiral_data(100, 3);



$dense1 = new Layer_Dense(2,64);

$activation1 = new Activation_Relu();


$dense2 = new Layer_Dense(64,3);

$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

$optimizer = new Optimizer_SGD();

for($i = 0; $i < 10000; $i++){

$dense1->forward($X);

$activation1->forward($dense1->output);

$dense2->forward($activation1->output);

$loss = $loss_activation->forward($dense2->output,$y);

$acc = np::accuracy($loss_activation->output,$y);

if (($i%100==0)) {
    // code...
    echo "epoc:$i acc: $acc , loss: $loss\n";    
}



$loss_activation->backward($loss_activation->output,$y);
$dense2->backward($loss_activation->dinputs);
$activation1->backward($dense2->dinputs);
$dense1->backward($activation1->dinput);
 	

if (($i%1000==0)) {
    // code...
    np::printMatrix($dense1->dweights,5);    
}
$optimizer->update_params($dense1);
if (($i%1000==0)) {
    // code...
    np::printMatrix($dense1->dweights,5);    
}
$optimizer->update_params($dense2);



}
// var_dump($dense1->dweights);
//  var_dump($dense1->dbiases);
//  var_dump($dense2->dweights);
// var_dump($dense2->dbiases);
}


function TEST_SOFTMAX(){

list($X, $y) =  np::spiral_data(100, 3);

$dense1 = new Layer_Dense(2,3);

$activation1 = new Activation_Relu();

$dense2 = new Layer_Dense(3,3);

$loss_activation = new Activation_Softmax();

$dense1->forward($X);

$activation1->forward($dense1->output);

$dense2->forward($activation1->output);

$loss = $loss_activation->forward($dense2->output,$y);


$Loss_CategoricalCrossentropy= new Loss_CategoricalCrossentropy();
$los = $Loss_CategoricalCrossentropy->calculate($loss_activation->output,$y);
np::printMatrix($loss_activation->output,5);

$acc = np::accuracy($loss_activation->output,$y);


echo "loss $los $acc";

 
}


function test_grapher(){
    list($X, $y) =  np::spiral_data(10, 3);

    $grapher = new Grapher();
    $grapher->addColor([255, 0, 0]); // Red color for class 0
    $grapher->addColor([0, 255, 0]); // Green color for class 1
    $grapher->addColor([0, 0, 255]); // Blue color for class 2

    $filename = 'graph.png';

    $grapher->createImage($X, $y, $filename);
}




TEST_SOFTMAX();

?>