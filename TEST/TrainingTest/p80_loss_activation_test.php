<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
list($X, $y) = NumpyLight::spiral_data(100, 3);

// Create layers and activations
$dense1 = new Layer_Dense(2, 64);
$activation1 = new Activation_ReLU();
$dense2 = new Layer_Dense(64, 3);
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
$optimizer = new Optimizer_SGD($learning_rate=1,$decay = 1e-2);

// // Train the network
for ($epoch = 0; $epoch <= 10000; $epoch++) {
	// echo "$epoch \n";

	$dense1->forward($X);
	$activation1->forward($dense1->output);
	$dense2->forward($activation1->output);
	$loss = $loss_activation->forward($dense2->output, $y);
	$predictions = NumpyLight::accuracy($loss_activation->output, $y);
	
	if (($epoch%100==0)) {
		echo "epoc: $epoch ,\tacc: $predictions\t,loss: $loss,\t lr: $optimizer->current_learning_rate \n";
	}

    # Backward pass

	$loss_activation->backward($loss_activation->output, $y);
	$dense2->backward($loss_activation->dinputs);
	$activation1->backward($dense2->dinputs);
	$dense1->backward($activation1->dinputs);
    
    // # Update weights and biases
	$optimizer->pre_update_params();
	$optimizer->update_params($dense1);
	$optimizer->update_params($dense2);
	
	$optimizer->post_update_params();

	

}



?>
