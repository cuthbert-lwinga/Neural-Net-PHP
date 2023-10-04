<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;


// Sample inputs and dvalues for testing
$sample_inputs = [
    [2.0, 3.0, 4.0],
    [1.0, -1.0, 0.5],
        [2.0, 3.0, 4.0],
    [1.0, -1.0, 0.5]
];
$sample_dvalues = [
    [0.1, 0.5, -0.6],
    [-0.1, 0.2, -0.1],
        [2.0, 3.0, 4.0],
    [1.0, -1.0, 0.5]
];

// Test the Activation_Softmax class
$activation_softmax = new Activation_Softmax();
$activation_softmax->forward($sample_inputs);
$forward_output = $activation_softmax->output;
$activation_softmax->backward($sample_dvalues);
$backward_output = $activation_softmax->dinputs;

NumpyLight::displayMatrix($forward_output);
echo "\n\n\n\n";
NumpyLight::displayMatrix($backward_output);
?>