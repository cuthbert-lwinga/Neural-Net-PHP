<?php
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;

// Initialize Optimizer_Adam instance
$optimizer = new Optimizer_Adam();

// Simulated Layer class to hold weights, biases, and gradients
class Layer {
    public $weights;
    public $biases;
    public $dweights;
    public $dbiases;
    public $weight_momentums;
    public $weight_cache;
    public $bias_momentums;
    public $bias_cache;
}

// Initialize a Layer instance with predefined weights and biases
$layer = new Layer();
$layer->weights = [[0.1, 0.2], [0.3, 0.4]];
$layer->biases = [0.1, 0.2];
$layer->dweights = [[0.01, 0.01], [0.01, 0.01]];
$layer->dbiases = [0.01, 0.01];

for ($i=0; $i < 4; $i++) {
    $optimizer->pre_update_params();
    $optimizer->update_params($layer);
    $optimizer->post_update_params();

echo " \n weight ($i): ".json_encode($layer->weights)." \n";
echo " \n Bias ($i): ".json_encode($layer->biases)." \n";
}


?>
