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

// Initialize layers
$dense1 = new Layer_Dense(1, 512);
$activation1 = new Activation_ReLU();

$dense2 = new Layer_Dense(512, 512);
$activation2 = new Activation_ReLU();

$dense3 = new Layer_Dense(512, 1);
$activation3 = new Activation_Linear();

// Initialize loss function and optimizer
$loss_function = new Loss_MeanSquaredError();
$optimizer = new Optimizer_Adam();

// var_dump($flattened_y);


// Calculate accuracy precision
$y_std = NumpyLight::std($y);
$accuracy_precision = $y_std/250;

// Training loop
for ($epoch = 0; $epoch <= 10000; $epoch++) {
    // Forward pass
    $dense1->forward($X);
    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);
    $activation2->forward($dense2->output);


    $dense3->forward($activation2->output);
    $activation3->forward($dense3->output);

    // Compute losses
    $data_loss = $loss_function->calculate($activation3->output, $y,false);
    $regularization_loss = $loss_function->regularization_loss($dense1) +
                           $loss_function->regularization_loss($dense2)+
                           $loss_function->regularization_loss($dense3);
    $loss = $data_loss + $regularization_loss;

    // Compute accuracy
    $predictions = $activation3->output;
    $accuracy = NumpyLight::calculateAccuracy($predictions, $y, $accuracy_precision);

    if ($epoch % 100 == 0) {
        echo "epoch: $epoch, acc: $accuracy, loss: $loss (data_loss: $data_loss, reg_loss: $regularization_loss), lr: {$optimizer->current_learning_rate}\n";
    }

    // Backward pass
    
    $loss_function->backward($activation3->output, $y);

    
    $activation3->backward($loss_function->dinputs);
    
    
    $dense3->backward($activation3->dinputs);
    
    $activation2->backward($dense3->dinputs);
    $startTime = microtime(true); // Start time
    $dense2->backward($activation2->dinputs);
    $endTime = microtime(true); // End time
    $executionTime = $endTime - $startTime; // Calculate execution time
    // echo "\n_________________________ time=> $executionTime seconds.\n";
    $activation1->backward($dense2->dinputs);
    $dense1->backward($activation1->dinputs);

    // echo "\nhere\n";
    // Update weights and biases
    
    $optimizer->pre_update_params();
    $optimizer->update_params($dense1);
    $optimizer->update_params($dense2);
    $optimizer->update_params($dense3);
    $optimizer->post_update_params();
    

}


$plotterTemp = new LinePlotter(500, 500);
$plotterTemp->setColor('blue', 0, 0, 255); // Blue color
$plotterTemp->setColor('red', 255, 0, 0); // Blue color

list($X_test, $y_test) = NumpyLight::sine_data();

$dense1->forward($X_test);
$activation1->forward($dense1->output);
$dense2->forward($activation1->output);
$activation2->forward($dense2->output);
$dense3->forward($activation2->output);
$activation3->forward($dense3->output);


$acc = NumpyLight::calculateAccuracy($activation3->output, $y_test,$accuracy_precision);

echo "\n\n validation, acc: $acc , loss: $loss \n\n";


$y_test = NumpyLight::reshape($y_test,[-1,1]);
$activation2->output = NumpyLight::reshape($activation2->output,[-1,1]);

$flattened_y_test = array_merge(...array_map('array_values', $y_test));
$activation2_y = array_merge(...array_map('array_values', $activation3->output));



$lines = [
    [
        'yValues' => $flattened_y_test,
        'color' => 'blue'
    ],
    [
        'yValues' => $activation2_y,
        'color' => 'red'
    ]
];

// Plot multiple lines using only y-values
$plotterTemp->plotMultipleLinesWithYOnly($lines);




$plotterTemp->save(basename($_SERVER['PHP_SELF'])."-Lineplot.png");






?>
