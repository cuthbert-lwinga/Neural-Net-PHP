<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
list($X, $y) = NumpyLight::vertical_data(100, 3);

// page i was reading 243
// Create layers and activations
$dense1 = new Layer_Dense(2, 3);
$activation1 = new Activation_ReLU();
$dense2 = new Layer_Dense(3, 3);
$activation2 = new Activation_Softmax();
$loss_function = new Loss_CategoricalCrossentropy();

# Helper variables
$lowest_loss = 9999999; # some initial value
$best_dense1_weights = $dense1->weights;
$best_dense1_biases = $dense1->biases;
$best_dense2_weights = $dense2->weights;
$best_dense2_biases = $dense2->biases;

// // Train the network
for ($epoch = 0; $epoch <= 10000; $epoch++) {
	$dense1->weights = NumpyLight::add($dense1->weights,NumpyLight::multiply(NumpyLight::random()->rand(2,3),0.05));
	$dense1->biases = NumpyLight::add($dense1->biases,NumpyLight::multiply(NumpyLight::random()->rand(1,3),0.05));
	$dense2->weights = NumpyLight::add($dense2->weights,NumpyLight::multiply(NumpyLight::random()->rand(3,3),0.05));
	$dense2->biases = NumpyLight::add($dense2->biases,NumpyLight::multiply(NumpyLight::random()->rand(1,3),0.05));


	# Perform a forward pass of our training data through this layer
	$dense1->forward($X);
	$activation1->forward($dense1->output);
	$dense2->forward($activation1->output);
	$activation2->forward($dense2->output);
	# Perform a forward pass through activation function
	# it takes the output of second dense layer here and returns loss
	$loss = $loss_function->calculate($activation2->output, $y);
	#Chapter 6 - Introducing Optimization - Neural Networks from Scratch in Python
	# Calculate accuracy from output of activation2 and targets
	# calculate values along first axis
	$accuracy = NumpyLight::accuracy($activation2->output, $y);
	// # If loss is smaller - print and save weights and biases aside
	if($loss < $lowest_loss){
		echo  "New set of weights found, iteration: $epoch, loss: $loss, acc: $accuracy \n";
	$best_dense1_weights = $dense1->weights;
	$best_dense1_biases = $dense1->biases;
	$best_dense2_weights = $dense2->weights;
	$best_dense2_biases = $dense2->biases;
	$lowest_loss = $loss;
}else{
		$dense1->weights = $best_dense1_weights;
		$dense1->biases = $best_dense1_biases;
		$dense2->weights = $best_dense2_weights;
		$dense2->biases = $best_dense2_biases;
	}

}



?>
