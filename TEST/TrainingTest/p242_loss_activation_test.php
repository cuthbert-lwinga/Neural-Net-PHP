<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;

list($X, $y) = NumpyLight::spiral_data(100, 3);
$dense1 = new Layer_Dense(2,3);
$activation1 = new Activation_ReLU();
$dense2 = new Layer_Dense(3,3);
$loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
$dense1->forward($X);
$activation1->forward($dense1->output);
$dense2->forward($activation1->output);
$loss = $loss_activation->forward($dense2->output,$y);
$acc = NumpyLight::accuracy($loss_activation->output, $y);
NumpyLight::displayMatrix($loss_activation->output);

echo "\nloss: $loss\n";

echo "\nacc: $acc\n";

//238

$loss_activation->backward($loss_activation->output, $y);
$dense2->backward($loss_activation->dinputs);
$activation1->backward($dense2->dinputs);
$dense1->backward($activation1->dinputs);
# Print gradients
NumpyLight::displayMatrix ($dense1->dweights);
echo "\n";
NumpyLight::displayMatrix ($dense1->dbiases);
echo "\n";
NumpyLight::displayMatrix ($dense2->dweights);
echo "\n";
NumpyLight::displayMatrix ($dense2->dbiases);

?>
