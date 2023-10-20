<?php
ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

$dataAnalyzed = "spiral_data";
list($X, $y) = NumpyLight::spiral_data(100, 3);
$filename = pathinfo(basename($_SERVER['SCRIPT_NAME']), PATHINFO_FILENAME);
// Create layers and activations
$dense1 = new Layer_Dense(2, 64); //,$weight_regularizer_l2 = 5e-4 ,$bias_regularizer_l2 = 5e-4;
$activation1 = new Activation_ReLU();
$dense2 = new Layer_Dense(64, 3);
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
$optimizer = new Optimizer_Adam($learning_rate = 0.02 , $decay = 5e-7 );
$lossTrend = [];
$accTrend = [];
$lrTrend = [];
$data_loss_arr = [];
$regularization_loss_arr = [];

$plotterTemp = new LinePlotter(500, 500);
$plotterTemp->plotPoints($X, $y);
$plotterTemp->save("images/".$filename."_$dataAnalyzed.png");

// // Train the network
for ($epoch = 0; $epoch <= 10000; $epoch++) {
	// echo "$epoch \n";

	$dense1->forward($X);
	$activation1->forward($dense1->output);
	$dense2->forward($activation1->output);
	
	$data_loss = $loss_activation->forward($dense2->output, $y);
	$regularization_loss = $loss_activation->loss->regularization_loss($dense1)+$loss_activation->loss->regularization_loss($dense2);
// https://instagram.com/daniellazoldyck?igshid=MzRlODBiNWFlZA==
	# Calculate overall loss
	$loss = $data_loss + $regularization_loss;

	$predictions = NumpyLight::accuracy($loss_activation->output, $y);
	
	if (($epoch%100==0)) {
		$lossTrend[] = $loss;
		$accTrend[] = $predictions;
		$lrTrend[] = $optimizer->current_learning_rate;
		$data_loss_arr[] = $data_loss;
		// $regularization_loss_arr[] = $regularization_loss;
		echo "epoc: $epoch ,\tacc: $predictions\t,loss: $loss, (data_loss: $data_loss, reg_loss: $regularization_loss),\t lr: $optimizer->current_learning_rate\n";
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


$plotter = new LinePlotter(500, 500);
$plotter->setColor('red', 255, 0, 0);
$plotter->plotLine($lossTrend, 'red');
$plotter->save("images/".$filename."_".$dataAnalyzed."_Loss_stat.png");

$plotter = new LinePlotter(500, 500);
$plotter->setColor('green', 0, 255, 0);
$plotter->plotLine($accTrend, 'green');
$plotter->save("images/".$filename."_".$dataAnalyzed."_Acc_stat.png");

$plotter = new LinePlotter(500, 500);
$plotter->setColor('blue', 0, 0, 255);
$plotter->plotLine($lrTrend, 'blue');
$plotter->save("images/".$filename."_".$dataAnalyzed."_lr_stat.png");

$plotter = new LinePlotter(500, 500);
$plotter->setColor('blue', 0, 0, 255);
$plotter->plotLine($data_loss_arr, 'blue');
$plotter->save("images/".$filename."_".$dataAnalyzed."_data_loss.png");

// $plotter = new LinePlotter(500, 500);
// $plotter->setColor('blue', 0, 0, 255);
// $plotter->plotLine($regularization_loss_arr, 'blue');
// $plotter->save("images/".$filename."_".$regularization_loss_arr."_regularization_loss.png");


?>
