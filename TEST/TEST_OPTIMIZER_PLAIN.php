<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

function PLAIN_OPTIMIZER(){
	list($X, $y) =  np::spiral_data(100, 3);

	$dense1 = new Layer_Dense(2, 64);
	$activation1 = new Activation_ReLU();
	$dense2 = new Layer_Dense(64, 3);
	$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
	$optimizer = new Optimizer_SGD();

	for ($epoch = 0; $epoch <= 10000; $epoch++) {
    // Perform a forward pass of our training data through this layer
		$dense1->forward($X);

    // Perform a forward pass through activation function
    // takes the output of the first dense layer here
		$activation1->forward($dense1->output);

    // Perform a forward pass through the second Dense layer
    // takes the outputs of the activation function of the first layer as inputs
		$dense2->forward($activation1->output);

    // Perform a forward pass through the activation/loss function
    // takes the output of the second dense layer here and returns loss
		$loss = $loss_activation->forward($dense2->output, $y);

    // Calculate accuracy from the output of activation2 and targets
    // calculate values along the first axis
		$accuracy = np::accuracy($loss_activation->output,$y);

	if ($epoch % 100 === 0) {
        echo "epoch: $epoch, acc: $accuracy, loss: $loss\n";
    }

    // Backward pass
		$loss_activation->backward($loss_activation->output, $y);
		$dense2->backward($loss_activation->dinputs);
		$activation1->backward($dense2->dinputs);
		$dense1->backward($activation1->dinput);

    // Update weights and biases
		$optimizer->update_params($dense1);
		$optimizer->update_params($dense2);
	}

}

function TEST_LEARNING_RATE_OPTIMIZER(){
	// Create dataset
list($X, $y) = np::spiral_data(100,3);

// Create Dense layer with 2 input features and 64 output values
$dense1 = new Layer_Dense(2, 64);

// Create ReLU activation (to be used with Dense layer)
$activation1 = new Activation_ReLU();

// Create second Dense layer with 64 input features and 3 output values
$dense2 = new Layer_Dense(64, 3);

// Create Softmax classifier's combined loss and activation
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

// Create optimizer
$optimizer = new Optimizer_SGD(learning_rate: 0.85);

// Train in loop
for ($epoch = 0; $epoch <= 10000; $epoch++) {
    // Perform a forward pass of our training data through the first layer
    $dense1->forward($X);

    // Perform a forward pass through activation function
    // takes the output of the first dense layer here
    $activation1->forward($dense1->output);

    // Perform a forward pass through the second Dense layer
    // takes the outputs of the activation function of the first layer as inputs
    $dense2->forward($activation1->output);

    // Perform a forward pass through the activation/loss function
    // takes the output of the second dense layer here and returns loss
    $loss = $loss_activation->forward($dense2->output, $y);

    // Calculate accuracy from output of activation2 and targets
    // calculate values along first axis

    $accuracy = np::accuracy($loss_activation->output,$y);

    if ($epoch % 100 == 0) {
        echo "epoch: $epoch, acc: $accuracy, loss: $loss\n";
    }

    // Backward pass
    $loss_activation->backward($loss_activation->output, $y);
    $dense2->backward($loss_activation->dinputs);
    $activation1->backward($dense2->dinputs);
    $dense1->backward($activation1->dinputs);

    // Update weights and biases
    $optimizer->update_params($dense1);
    $optimizer->update_params($dense2);
}

}

function TEST_CURRENT_LEARNING_OPTIMIZER(){
	// Create dataset
list($X, $y) = np::spiral_data(100, 3);

// Create Dense layer with 2 input features and 64 output values
$dense1 = new Layer_Dense(2, 64);

// Create ReLU activation (to be used with Dense layer)
$activation1 = new Activation_ReLU();

// Create second Dense layer with 64 input features and 3 output values
$dense2 = new Layer_Dense(64, 3);

// Create Softmax classifier's combined loss and activation
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

// Create optimizer
$optimizer = new Optimizer_SGD(1,1e-3);

// Train in loop
for ($epoch = 0; $epoch <= 10000; $epoch++) {
    // Perform a forward pass of our training data through the first layer
    $dense1->forward($X);

    // Perform a forward pass through activation function
    // takes the output of the first dense layer here
    $activation1->forward($dense1->output);

    // Perform a forward pass through the second Dense layer
    // takes the outputs of the activation function of the first layer as inputs
    $dense2->forward($activation1->output);

    // Perform a forward pass through the activation/loss function
    // takes the output of the second dense layer here and returns loss
    $loss = $loss_activation->forward($dense2->output, $y);

    // Calculate accuracy from output of activation2 and targets
    // calculate values along first axis
   $accuracy = np::accuracy($loss_activation->output,$y);
    if ($epoch % 100 == 0) {
        echo "epoch: $epoch, acc: $accuracy, loss: $loss, lr: {$optimizer->current_learning_rate}\n";
    }

    // Backward pass
    $loss_activation->backward($loss_activation->output, $y);
    $dense2->backward($loss_activation->dinputs);
    $activation1->backward($dense2->dinputs);
    $dense1->backward($activation1->dinput);

    // Update weights and biases
    $optimizer->pre_update_params();
    $optimizer->update_params($dense1);
    $optimizer->update_params($dense2);
    $optimizer->post_update_params();
}

}
PLAIN_OPTIMIZER();
// TEST_LEARNING_RATE_OPTIMIZER();
//TEST_CURRENT_LEARNING_OPTIMIZER();
?>