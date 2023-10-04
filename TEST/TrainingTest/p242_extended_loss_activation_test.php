<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;

// Manually set X and y
$X = array(
    array(0.1, 0.2),
    array(0.3, 0.4),
    array(0.5, 0.6),
    array(0.7, 0.8),
    array(0.9, 1.0),
    array(-0.1, -0.2),
    array(-0.3, -0.4),
    array(-0.5, -0.6),
    array(-0.7, -0.8),
    array(-0.9, -1.0)
);
$y = array(0, 1, 2, 0, 1, 2, 0, 1, 2, 0);

$dense1 = new Layer_Dense(2,3);
$activation1 = new Activation_ReLU();
$dense2 = new Layer_Dense(3,3);

// Manually setting weights and biases
$dense1->weights = array(
    array(0.1, -0.2, 0.3),
    array(0.4, 0.5, -0.6)
);
$dense1->biases = array(array(0.1, -0.2, 0.3));

$dense2->weights = array(
    array(-0.1, 0.2, -0.3),
    array(0.4, -0.5, 0.6),
    array(-0.7, 0.8, -0.9)
);
$dense2->biases = array(array(-0.1, 0.2, -0.3));

$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
$dense1->forward($X);
$activation1->forward($dense1->output);
$dense2->forward($activation1->output);
$loss = $loss_activation->forward($dense2->output,$y);
$acc = NumpyLight::accuracy($loss_activation->output, $y);

NumpyLight::displayMatrix($loss_activation->output);

echo "\nloss: $loss\n";
echo "\nacc: $acc\n";

$loss_activation->backward($loss_activation->output, $y);
$dense2->backward($loss_activation->dinputs);
$activation1->backward($dense2->dinputs);
$dense1->backward($activation1->dinputs);
// Print gradients
NumpyLight::displayMatrix($dense1->dweights);
echo "\n\n";
NumpyLight::displayMatrix($dense1->dbiases);
echo "\n";
NumpyLight::displayMatrix($dense2->dweights);
echo "\n";
NumpyLight::displayMatrix($dense2->dbiases);
?>
