<?php
ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

list($X, $y) = NumpyLight::spiral_data(100,3);

$validation = NumpyLight::spiral_data(100,3);


echo "\n\nModel Init\n\n";
$Model = new Model();
$Model->add(new Layer_Dense(2,512,$weight_regularizer_l2 = 5e-4 ,$bias_regularizer_l2 = 5e-4));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dropout(0.0));
$Model->add(new Layer_Dense(512,3));
$Model->add(new Activation_Softmax());
$Model->set(
	$loss_function = new Loss_CategoricalCrossentropy(),
	$optimizer = new Optimizer_Adam($learning_rate = 0.05, $decay = 5e-5),
	$accuracy = new Accuracy_Categorical()
);

$Model->finalize();

$Model->train($X, $y,$epoch = 10000, $batch_size = NULL,$print_every = 100,$validation_data = $validation);

?>