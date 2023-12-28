<?php
ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

list($X, $y) = NumpyLight::sine_data();


$Model = new Model();
$Model->add(new Layer_Dense(1,64));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dense(64,64));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dense(64,1));
$Model->add(new Activation_Linear());
$Model->set(
	$loss_function = new Loss_MeanSquaredError(),
	$optimizer = new Optimizer_Adam($learning_rate = 0.005, $decay = 1e-3),
	$accuracy = new Accuracy_Regression()
);

$Model->finalize();


$Model->train($X, $y,$epoch = 1000,$print_every = 100);

?>