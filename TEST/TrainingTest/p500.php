<?php
ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

list($X, $y) = NumpyLight::spiral_data(100,2);

$y = NumpyLight::reshape($y,[-1,1]);

$validation = NumpyLight::spiral_data(100,2);


$Model = new Model();
$Model->add(new Layer_Dense(2,64,$weight_regularizer_l2 = 5e-4 ,$bias_regularizer_l2 = 5e-4));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dense(64,1));
$Model->add(new Activation_Sigmoid());
$Model->set(
	$loss_function = new Loss_BinaryCrossentropy(),
	$optimizer = new Optimizer_Adam($learning_rate = 0.001, $decay = 5e-7),
	$accuracy = new Accuracy_Categorical()
);

$Model->finalize();


$Model->train($X, $y,$epoch = 10000,$print_every = 100,$validation_data = $validation);

?>