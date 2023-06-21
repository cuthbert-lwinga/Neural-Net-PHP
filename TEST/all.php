<?php
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

class Layer_Dense {
    public $weights;
    public $biases;
    public $inputs;
    public $output;
    public $dweights;
    public $dbiases;
    public $dinputs;

    public function __construct($n_inputs, $n_neurons) {
        $this->weights = 0.01 * random.randn($n_inputs, $n_neurons);
        $this->biases = zeros([1, $n_neurons]);
    }

    public function forward($inputs) {
        $this->inputs = $inputs;
        $this->output = dot($inputs, $this->weights) + $this->biases;
    }

    public function backward($dvalues) {
        $this->dweights = dot($this->inputs->T, $dvalues);
        $this->dbiases = sum($dvalues, 0, true);
        $this->dinputs = dot($dvalues, $this->weights->T);
    }
}

class Activation_ReLU {
    public $inputs;
    public $output;
    public $dinputs;

    public function forward($inputs) {
        $this->inputs = $inputs;
        $this->output = maximum(0, $inputs);
    }

    public function backward($dvalues) {
        $this->dinputs = $dvalues->copy();
        $this->dinputs[$this->inputs <= 0] = 0;
    }
}

class Activation_Softmax {
    public $inputs;
    public $output;
    public $dinputs;

    public function forward($inputs) {
        $this->inputs = $inputs;
        $exp_values = exp($inputs - max($inputs, 1));
        $this->output = $exp_values / sum($exp_values, 1);
    }

    public function backward($dvalues) {
        $this->dinputs = zeros_like($dvalues);
        foreach (array_map(null, $this->output, $dvalues) as $index => [$single_output, $single_dvalues]) {
            $single_output = reshape($single_output, [-1, 1]);
            $jacobian_matrix = diagflat($single_output) - dot($single_output, $single_output->T);
            $this->dinputs[$index] = dot($jacobian_matrix, $single_dvalues);
        }
    }
}

class Loss {
    public function calculate($output, $y) {
        $sample_losses = $this->forward($output, $y);
        $data_loss = mean($sample_losses);
        return $data_loss;
    }
}

class Loss_CategoricalCrossentropy extends Loss {
    public function forward($y_pred, $y_true) {
        $samples = count($y_pred);
        $y_pred_clipped = clip($y_pred, 1e-7, 1 - 1e-7);
        if (count($y_true->shape) === 1) {
            $correct_confidences = $y_pred_clipped[array_map(null, range($samples), $y_true)];
        } elseif (count($y_true->shape) === 2) {
            $correct_confidences = sum($y_pred_clipped * $y_true, 1);
        }
        $negative_log_likelihoods = -log($correct_confidences);
        return $negative_log_likelihoods;
    }

    public function backward($dvalues, $y_true) {
        $samples = count($dvalues);
        $labels = count($dvalues[0]);
        if (count($y_true->shape) === 1) {
            $y_true = eye($labels)[$y_true];
        }
        $this->dinputs = -$y_true / $dvalues;
        $this->dinputs = $this->dinputs / $samples;
    }
}

class Activation_Softmax_Loss_CategoricalCrossentropy {
    public $activation;
    public $loss;
    public $output;
    public $dinputs;

    public function __construct() {
        $this->activation = new Activation_Softmax();
        $this->loss = new Loss_CategoricalCrossentropy();
    }

    public function forward($inputs, $y_true) {
        $this->activation->forward($inputs);
        $this->output = $this->activation->output;
        return $this->loss->calculate($this->output, $y_true);
    }

    public function backward($dvalues, $y_true) {
        $samples = count($dvalues);
        if (count($y_true->shape) === 2) {
            $y_true = argmax($y_true, 1);
        }
        $this->dinputs = $dvalues->copy();
        $this->dinputs[array_map(null, range($samples), $y_true)] -= 1;
        $this->dinputs = $this->dinputs / $samples;
    }
}

class Optimizer_SGD {
    public $learning_rate;

    public function __construct($learning_rate = 1.0) {
        $this->learning_rate = $learning_rate;
    }

    public function update_params($layer) {
        $layer->weights -= $this->learning_rate * $layer->dweights;
        $layer->biases -= $this->learning_rate * $layer->dbiases;
    }
}

// Create dataset
[$X, $y] = spiral_data(100, 3);

// Create Dense layer with 2 input features and 64 output values
$dense1 = new Layer_Dense(2, 64);

// Create ReLU activation (to be used with Dense layer)
$activation1 = new Activation_ReLU();

// Create second Dense layer with 64 input features and 3 output values
$dense2 = new Layer_Dense(64, 3);

// Create Softmax classifier's combined loss and activation
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

// Create optimizer
$optimizer = new Optimizer_SGD();

// Train in loop
for ($epoch = 0; $epoch <= 10000; $epoch++) {
    // Perform a forward pass of our training data through the layers
    $dense1->forward($X);
    $activation1->forward($dense1->output);
    $dense2->forward($activation1->output);

    // Perform a forward pass through the activation/loss function and calculate loss
    $loss = $loss_activation->forward($dense2->output, $y);

    // Calculate accuracy
    $predictions = argmax($loss_activation->output, 1);
    if (count($y->shape) === 2) {
        $y = argmax($y, 1);
    }
    $accuracy = mean($predictions === $y);

    if ($epoch % 100 === 0) {
        echo "epoch: $epoch, acc: $accuracy, loss: $loss\n";
    }

    // Perform backward pass
    $loss_activation->backward($loss_activation->output, $y);
    $dense2->backward($loss_activation->dinputs);
    $activation1->backward($dense2->dinputs);
    $dense1->backward($activation1->dinputs);

    // Update weights and biases
    $optimizer->update_params($dense1);
    $optimizer->update_params($dense2);
}
?>