<?php
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationSigmoid\Activation_Sigmoid;

// Initialize Optimizer_Adam instance
$optimizer = new Optimizer_Adam();
$Activation_Sigmoid = new Activation_Sigmoid();
// var_dump(NumpyLight::subtract(1,[[3,20]]));

$temp = [[ 1 , 2 , 3 ],
[ 2 , 4 , 6 ],
[ 0 , 5 , 10 ],
[ 11 , 12 , 13 ],
[ 5 , 10 , 15 ]];

    $y_pred = [
        [0.7, 0.2, 0.1],
        [0.5, 0.5, 0.5],
        [0.2, 0.8, 0.9]
    ];
    $y_true = [
        [1, 0, 0],
        [0, 1, 0],
        [0, 0, 1]
    ];
    
    # Create an instance
    $loss = new Loss_BinaryCrossentropy();
    
    # Forward pass
    $loss_value = $loss->forward($y_pred, $y_true);
    print("Loss Value:");
    var_dump($loss_value);
    
    # Mock backward values (typically, the derivative of the loss with respect to the network's output)
    $dvalues = NumPyLight::subtract($y_pred ,$y_true);
    
    // # Backward pass
    $dinputs = $loss->backward($dvalues, $y_true);
    print("\nGradient (dinputs): \n");
    NumPyLight::displayMatrix($loss->dinputs);

// $Activation_Sigmoid->forward($temp);

// $Activation_Sigmoid->backward($temp);

// NumpyLight::displayMatrix($Activation_Sigmoid->dinputs);

// NumpyLight::displayMatrix($Activation_Sigmoid->dinputs);

// $inf = (NumpyLight::log([0,0,0,0]));

// // var_dump(NumpyLight::clip($inf, 1e-7, 1 - 1e-7));


// var_dump(NumpyLight::reshape([0,0,0,0,0,0],[-1,1]));

?>
