<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;

// Load JSON data
$json_data = file_get_contents('/Users/cuthbertlwinga/Documents/projects/PHP/Neural-Net-PHP/TEST/Neural_Networks_Components/neural_network_data.json');
// echo $_SERVER["DOCUMENT_ROOT"].'/TEST/TrainingTest/neural_network_data.json';
// $json_data = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/TEST/TrainingTest/neural_network_data.json');
// $data = json_decode($json_data, true);

// // Extract spiral_data
// list($X,$y) = $data['spiral_data'];

// $dense1 = new Layer_Dense(2,64);

// // Set weights for dense layer
// $dense1->weights = $data['weights'];
// $dense1->biases = $data['biases'];

// $dense1->forward($X);
// $data['output'];

?>
