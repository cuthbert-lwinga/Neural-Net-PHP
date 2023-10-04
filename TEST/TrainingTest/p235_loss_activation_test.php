<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;

list($X, $y) = NumpyLight::spiral_data(100, 3);

// Test the function
$matrix = [
    [1, 2, 3, 4, 5, 6],
    [7, 8, 9, 10, 11, 12],
    [13, 14, 15, 16, 17, 18]
];

$softmax_outputs = [[0.7, 0.1, 0.2], [0.1, 0.5, 0.4], [0.02, 0.9, 0.08]];
$class_targets = [0, 1, 1];


$softmax_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
$softmax_loss->backward($softmax_outputs, $class_targets);

$dvalues1 = $softmax_loss->dinputs;
$activation = new Activation_Softmax();
$activation->output = $softmax_outputs;
$loss = new Loss_CategoricalCrossentropy();
$loss->backward($softmax_outputs, $class_targets);
$activation->backward($loss->dinputs);
$dvalues2 = $activation->dinputs;

print_r ( 'Gradients: combined loss and activation:' );
echo "\n";
NumpyLight::displayMatrix($dvalues1);
print_r ( 'Gradients: separate loss and activation:' );
echo "\n";
NumpyLight::displayMatrix($dvalues2);
?>
